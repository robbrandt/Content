<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Controller;

use Zikula\ContentModule\Controller\Base\AbstractContentItemController;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Form\Type\ContentItemType;
use Zikula\ContentModule\Form\Type\MoveCopyContentItemType;
use Zikula\ContentModule\Helper\ContentDisplayHelper;
use Zikula\ContentModule\Helper\HookHelper;
use Zikula\ContentModule\Helper\LoggableHelper;
use Zikula\ContentModule\Helper\ModelHelper;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ContentModule\Helper\TranslatableHelper;
use Zikula\ContentModule\Helper\WorkflowHelper;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Content item controller class providing navigation and interaction functionality.
 */
class ContentItemController extends AbstractContentItemController
{
    /**
     * @Route("/admin/contentItems",
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminIndexAction(
        Request $request,
        PermissionHelper $permissionHelper
    ): Response {
        return $this->indexInternal($request, $permissionHelper, true);
    }
    
    /**
     * @Route("/contentItems",
     *        methods = {"GET"}
     * )
     */
    public function indexAction(
        Request $request,
        PermissionHelper $permissionHelper
    ): Response {
        return $this->indexInternal($request, $permissionHelper, false);
    }
    
    /**
     * This action displays a content item in editing mode.
     *
     * @Route("/item/displayEditing/{contentItem}", requirements = {"contentItem" = "\d+"}, options={"expose"=true})
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if item isn't found
     * @throws RuntimeException Thrown if item type isn't found
     */
    public function displayEditingAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ContentDisplayHelper $contentDisplayHelper,
        ?ContentItemEntity $contentItem = null
    ): JsonResponse {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->trans('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }

        if (null === $contentItem) {
            throw new NotFoundHttpException($this->trans('No such content found.'));
        }

        if (!$permissionHelper->mayEdit($contentItem)) {
            throw new AccessDeniedException();
        }

        $editDetails = $contentDisplayHelper->getDetailsForDisplayEditing($contentItem);

        return $this->json($editDetails);
    }

    /**
     * This action duplicates a given content items.
     *
     * @Route("/item/duplicate/{contentItem}", requirements = {"contentItem" = "\d+"}, options={"expose"=true})
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page or content item isn't found
     */
    public function duplicateAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        ModelHelper $modelHelper,
        LoggableHelper $loggableHelper,
        HookHelper $hookHelper,
        ?ContentItemEntity $contentItem = null
    ): JsonResponse {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->trans('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }

        if (null === $contentItem) {
            throw new NotFoundHttpException($this->trans('No such content found.'));
        }

        if (!$permissionHelper->mayEdit($contentItem)) {
            throw new AccessDeniedException();
        }

        $pageId = $request->request->getInt('pageId');
        if (1 > $pageId) {
            throw new RuntimeException($this->trans('Invalid input received.'));
        }

        /** @var PageEntity $page */
        $page = $entityFactory->getRepository('page')->selectById($pageId);
        if (null === $page) {
            throw new NotFoundHttpException($this->trans('Page not found.'));
        }

        $newItem = clone $contentItem;

        if ($newItem->supportsHookSubscribers()) {
            // Let any ui hooks perform additional validation actions
            $validationErrors = $hookHelper->callValidationHooks($newItem, UiHooksCategory::TYPE_VALIDATE_EDIT);
            if (count($validationErrors) > 0) {
                return $this->json(['message' => implode(' ', $validationErrors)], Response::HTTP_BAD_REQUEST);
            }
        }

        $page->addContentItems($newItem);

        $success = $workflowHelper->executeAction($newItem, 'submit');
        if (!$success) {
            return $this->json(
                ['message' => $this->trans('Error! An error occured during saving the content.')],
                Response::HTTP_BAD_REQUEST
            );
        }

        $modelHelper->cloneContentTranslations($contentItem->getId(), $newItem->getId());

        if ($newItem->supportsHookSubscribers()) {
            // Let any ui hooks know that we have updated the content item
            $hookHelper->callProcessHooks($newItem, UiHooksCategory::TYPE_PROCESS_EDIT);
        }

        $page->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CONTENT_CLONED');
        $loggableHelper->updateContentData($page);
        /*$success = */$workflowHelper->executeAction($page, 'update');

        return $this->json([
            'id' => $newItem->getId(),
            'message' => $this->trans('Done! Content saved.'),
        ]);
    }

    /**
     * This action provides a handling of edit requests for content items.
     *
     * @Route("/item/edit/{contentItem}", requirements = {"contentItem" = "\d+"}, options={"expose"=true})
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if item to be edited isn't found
     * @throws RuntimeException Thrown if item type isn't found
     */
    public function editAction(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        LoggableHelper $loggableHelper,
        ContentDisplayHelper $contentDisplayHelper,
        TranslatableHelper $translatableHelper,
        HookHelper $hookHelper,
        ?ContentItemEntity $contentItem = null
    ): JsonResponse {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->trans('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }

        $contentTypeClass = null;
        $isCreation = false;

        $isPost = $request->isMethod('POST');
        $dataSource = $isPost ? $request->request : $request->query;

        // permission check
        if (null === $contentItem) {
            $isCreation = true;
            if (!$permissionHelper->hasComponentPermission('page', ACCESS_ADD)) {
                throw new AccessDeniedException();
            }
            $pageId = $dataSource->getInt('pageId');
            $contentTypeClass = $request->query->get('type', '');
            if ($pageId < 1 || empty($contentTypeClass) || !class_exists($contentTypeClass)) {
                throw new RuntimeException($this->trans('Invalid input received.'));
            }

            /** @var PageEntity $page */
            $page = $entityFactory->getRepository('page')->selectById($pageId);
            if (null === $page) {
                throw new NotFoundHttpException($this->trans('Page not found.'));
            }

            $contentItem = $entityFactory->createContentItem();
            $contentItem->setOwningType($contentTypeClass);
            $page->addContentItems($contentItem);
        } else {
            if (!$permissionHelper->mayEdit($contentItem)) {
                throw new AccessDeniedException();
            }
            $page = $contentItem->getPage();
        }

        $contentType = null;
        $templateParameters = [];
        $action = $dataSource->get('action', '');
        $form = null;
        try {
            $contentType = $contentDisplayHelper->initContentType($contentItem);

            if (true === $isCreation) {
                $contentItem->setContentData($contentType->getDefaultData());
            }

            if ($isCreation) {
                $routeArgs = [
                    'type' => $contentTypeClass,
                ];
            } else {
                $routeArgs = [
                    'contentItem' => $contentItem->getId(),
                ];
            }
            $route = $router->generate('zikulacontentmodule_contentitem_edit', $routeArgs);

            $form = $this->createForm(ContentItemType::class, $contentItem, [
                'action' => $route,
                'content_type' => $contentType,
            ]);

            $templateParameters = [
                'mode' => $isCreation ? 'create' : 'edit',
                'contentItem' => $contentItem,
                'form' => $form->createView(),
                'contentType' => $contentType,
                'contentFormTemplate' => $contentType->getEditTemplatePath(),
                'supportsHookSubscribers' => $contentItem->supportsHookSubscribers(),
            ];

            if ($contentItem->supportsHookSubscribers()) {
                // Call form aware display hooks
                $formHook = $hookHelper->callFormDisplayHooks($form, $contentItem, FormAwareCategory::TYPE_EDIT);
                $templateParameters['formHookTemplates'] = $formHook->getTemplates();
            }
        } catch (RuntimeException $exception) {
            // content type is not available anymore
            if ('delete' !== $action) {
                throw $exception;
            }
        }

        if ($isPost) {
            if (!in_array($action, ['save', 'delete'])) {
                throw new RuntimeException($this->trans('Invalid action received.'));
            }

            if (null !== $form) {
                $form->handleRequest($request);
                //if ($form->isValid()) // TODO investigate
            }
            if ('save' === $action) {
                if (true === $isCreation) {
                    $page->addContentItems($contentItem);
                }

                if ($contentType instanceof AbstractContentType) {
                    $contentType->setEntity($contentItem);
                }
                $contentItem->setSearchText($contentType->getSearchableText());

                $workflowAction = $isCreation ? 'submit' : 'update';

                if ($contentItem->supportsHookSubscribers()) {
                    // Let any ui hooks perform additional validation actions
                    $validationErrors = $hookHelper->callValidationHooks(
                        $contentItem,
                        UiHooksCategory::TYPE_VALIDATE_EDIT
                    );
                    if (count($validationErrors) > 0) {
                        return $this->json(['message' => implode(' ', $validationErrors)], Response::HTTP_BAD_REQUEST);
                    }
                }

                // execute the workflow action
                $success = $workflowHelper->executeAction($contentItem, $workflowAction);
                if (!$success) {
                    return $this->json(
                        ['message' => $this->trans('Error! An error occured during saving the content.')],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                // sync non-translatable fields (which may have changed) with other translations
                $translatableFields = $contentType->getTranslatableDataFields();
                $contentData = $contentItem->getContentData();
                $nonTranslatableContentData = [];
                foreach ($contentData as $fieldName => $fieldValue) {
                    if (in_array($fieldName, $translatableFields, true)) {
                        continue;
                    }
                    $nonTranslatableContentData[$fieldName] = $fieldValue;
                }
                if (count($nonTranslatableContentData) > 0) {
                    $entityManager = $entityFactory->getEntityManager();
                    $translations = $translatableHelper->prepareEntityForEditing($contentItem);
                    foreach ($translations as $language => $translationData) {
                        foreach ($nonTranslatableContentData as $fieldName => $fieldValue) {
                            $translations[$language]['contentData'][$fieldName] = $fieldValue;
                        }
                        foreach ($translations[$language] as $fieldName => $fieldValue) {
                            $contentItem[$fieldName] = $fieldValue;
                        }
                        $contentItem->setLocale($language);
                        $entityManager->flush();
                    }
                }

                if ($contentItem->supportsHookSubscribers()) {
                    // Call form aware processing hooks
                    $hookHelper->callFormProcessHooks($form, $contentItem, FormAwareCategory::TYPE_PROCESS_EDIT);

                    // Let any ui hooks know that we have updated the content item
                    $hookHelper->callProcessHooks($contentItem, UiHooksCategory::TYPE_PROCESS_EDIT);
                }

                if (true === $isCreation) {
                    $page->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CONTENT_CREATED');
                } else {
                    $page->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CONTENT_UPDATED');
                }
                $loggableHelper->updateContentData($page);
                /*$success = */$workflowHelper->executeAction($page, 'update');

                return $this->json(['id' => $contentItem->getId(), 'message' => $this->trans('Done! Content saved.')]);
            }
            if ('delete' === $action) {
                // determine available workflow actions
                $actions = $workflowHelper->getActionsForObject($contentItem);
                if (false === $actions || !is_array($actions)) {
                    throw new RuntimeException($this->trans('Error! Could not determine workflow actions.'));
                }

                // check whether deletion is allowed
                $deleteActionId = 'delete';
                $deleteAllowed = false;
                foreach ($actions as $actionId => $action) {
                    if ($actionId !== $deleteActionId) {
                        continue;
                    }
                    $deleteAllowed = true;
                    break;
                }
                if (!$deleteAllowed) {
                    return $this->json(
                        ['message' => $this->trans('Error! It is not allowed to delete this content item.')],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                if ($contentItem->supportsHookSubscribers()) {
                    // Let any ui hooks perform additional validation actions
                    $validationErrors = $hookHelper->callValidationHooks(
                        $contentItem,
                        UiHooksCategory::TYPE_VALIDATE_DELETE
                    );
                    if (0 < count($validationErrors)) {
                        return $this->json(['message' => implode(' ', $validationErrors)], Response::HTTP_BAD_REQUEST);
                    }
                }

                // execute the workflow action
                $success = $workflowHelper->executeAction($contentItem, $deleteActionId);

                if (!$success) {
                    return $this->json(
                        ['message' => $this->trans('Error! An error occured during content deletion.')],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                if (null !== $form && $contentItem->supportsHookSubscribers()) {
                    // Call form aware processing hooks
                    $hookHelper->callFormProcessHooks($form, $contentItem, FormAwareCategory::TYPE_PROCESS_DELETE);

                    // Let any ui hooks know that we have deleted the content item
                    $hookHelper->callProcessHooks($contentItem, UiHooksCategory::TYPE_PROCESS_DELETE);
                }

                $page->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CONTENT_DELETED');
                $loggableHelper->updateContentData($page);
                /*$success = */$workflowHelper->executeAction($page, 'update');

                return $this->json(['message' => $this->trans('Done! Content deleted.')]);
            }
        }

        $template = (!$isPost ? 'edit' : 'editFormBody') . '.html.twig';

        $output = [
            'form' => $this->renderView('@ZikulaContentModule/ContentItem/' . $template, $templateParameters),
            'assets' => $contentType->getAssets(ContentTypeInterface::CONTEXT_EDIT),
            'jsEntryPoint' => $contentType->getJsEntrypoint(ContentTypeInterface::CONTEXT_EDIT),
        ];

        if (!$isPost) {
            return $this->json($output);
        }

        $output['message'] = $this->trans('Error! Please check your input.');

        return $this->json($output, Response::HTTP_BAD_REQUEST);
    }

    /**
     * This action provides a handling of move and copy requests for content items.
     *
     * @Route("/item/movecopy/{contentItem}", requirements = {"contentItem" = "\d+"}, options={"expose"=true})
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if item to be moved/copied isn't found
     */
    public function movecopyAction(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        ModelHelper $modelHelper,
        LoggableHelper $loggableHelper,
        HookHelper $hookHelper,
        ?ContentItemEntity $contentItem = null
    ): JsonResponse {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->trans('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }

        $isPost = $request->isMethod('POST');

        // permission check
        if (null === $contentItem) {
            throw new NotFoundHttpException($this->trans('Content item not found.'));
        }
        if (!$permissionHelper->mayEdit($contentItem)) {
            throw new AccessDeniedException();
        }

        $routeArgs = [
            'contentItem' => $contentItem->getId(),
        ];
        $route = $router->generate('zikulacontentmodule_contentitem_movecopy', $routeArgs);

        $formData = $isPost ? $request->request->get('zikulacontentmodule_movecopycontentitem') : [];
        $operationType = $isPost && isset($formData['operationType']) ? $formData['operationType'] : 'copy';
        if (!in_array($operationType, ['move', 'copy'], true)) {
            $operationType = 'copy';
        }

        $form = $this->createForm(MoveCopyContentItemType::class, [
            'operationType' => $operationType,
        ], [
            'action' => $route,
        ]);

        $templateParameters = [
            'contentItem' => $contentItem,
            'form' => $form->createView(),
        ];

        if ($isPost) {
            $form->handleRequest($request);
            //if ($form->isValid()) // TODO investigate

            $sourcePageId = $contentItem->getPage()->getId();
            $destinationPageId = $formData['destinationPage'];
            if (!$sourcePageId) {
                throw new NotFoundHttpException($this->trans('Source page not found.'));
            }
            if (!$destinationPageId) {
                throw new NotFoundHttpException($this->trans('Destination page not found.'));
            }
            if ($sourcePageId === $destinationPageId) {
                throw new RuntimeException($this->trans('Destination page must not be the current page.'));
            }
            /** @var PageEntity $sourcePage */
            $sourcePage = $entityFactory->getRepository('page')->selectById($sourcePageId);
            if (null === $sourcePage) {
                throw new NotFoundHttpException($this->trans('Source page not found.'));
            }
            /** @var PageEntity $destinationPage */
            $destinationPage = $entityFactory->getRepository('page')->selectById($destinationPageId);
            if (null === $destinationPage) {
                throw new NotFoundHttpException($this->trans('Destination page not found.'));
            }

            if ('move' === $operationType) {
                $sourcePage->removeContentItems($contentItem);
                $destinationPage->addContentItems($contentItem);
                $success = $workflowHelper->executeAction($contentItem, 'update');
                if (!$success) {
                    return $this->json(
                        ['message' => $this->trans('Error! An error occured during saving the content.')],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                $sourcePage->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CONTENT_DELETED');
                $loggableHelper->updateContentData($sourcePage);
                /*$success = */$workflowHelper->executeAction($sourcePage, 'update');

                $destinationPage->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CONTENT_CREATED');
                $loggableHelper->updateContentData($destinationPage);
                /*$success = */$workflowHelper->executeAction($destinationPage, 'update');
            } elseif ('copy' === $operationType) {
                $newItem = clone $contentItem;

                if ($newItem->supportsHookSubscribers()) {
                    // Let any ui hooks perform additional validation actions
                    $validationErrors = $hookHelper->callValidationHooks(
                        $newItem,
                        UiHooksCategory::TYPE_VALIDATE_EDIT
                    );
                    if (count($validationErrors) > 0) {
                        return $this->json(
                            ['message' => implode(' ', $validationErrors)],
                            Response::HTTP_BAD_REQUEST
                        );
                    }
                }

                $destinationPage->addContentItems($newItem);

                $success = $workflowHelper->executeAction($newItem, 'submit');
                if (!$success) {
                    return $this->json(
                        ['message' => $this->trans('Error! An error occured during saving the content.')],
                        Response::HTTP_BAD_REQUEST
                    );
                }

                $modelHelper->cloneContentTranslations($contentItem->getId(), $newItem->getId());

                if ($newItem->supportsHookSubscribers()) {
                    // Let any ui hooks know that we have updated the content item
                    $hookHelper->callProcessHooks($newItem, UiHooksCategory::TYPE_PROCESS_EDIT);
                }

                $sourcePage->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CONTENT_CLONED');
                $loggableHelper->updateContentData($sourcePage);
                /*$success = */$workflowHelper->executeAction($sourcePage, 'update');
                $destinationPage->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CONTENT_CREATED');
                $loggableHelper->updateContentData($destinationPage);
                /*$success = */$workflowHelper->executeAction($destinationPage, 'update');
            }

            return $this->json([
                'id' => $contentItem->getId(),
                'message' => 'move' === $operationType
                    ? $this->trans('Done! Content moved.')
                    : $this->trans('Done! Content copied.'),
            ]);
        }

        $template = (!$isPost ? 'moveCopy' : 'moveCopyFormBody') . '.html.twig';

        $output = [
            'form' => $this->renderView('@ZikulaContentModule/ContentItem/' . $template, $templateParameters),
        ];

        if (!$isPost) {
            return $this->json($output);
        }

        $output['message'] = $this->trans('Error! Please check your input.');

        return $this->json($output, Response::HTTP_BAD_REQUEST);
    }
}
