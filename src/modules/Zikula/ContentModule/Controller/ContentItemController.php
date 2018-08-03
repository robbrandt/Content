<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Controller;

use Zikula\ContentModule\Controller\Base\AbstractContentItemController;

use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\HookBundle\Category\FormAwareCategory;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\ContentModule\ContentTypeInterface;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Form\Type\ContentItemType;
use Zikula\ThemeModule\Engine\Annotation\Theme;

/**
 * Content item controller class providing navigation and interaction functionality.
 */
class ContentItemController extends AbstractContentItemController
{
    /**
     * @inheritDoc
     *
     * @Route("/admin/contentItems",
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminIndexAction(Request $request)
    {
        return parent::adminIndexAction($request);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/contentItems",
     *        methods = {"GET"}
     * )
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }
    
    /**
     * This action displays the a content items in editing mode.
     *
     * @Route("/item/displayEditing/{contentItem}", requirements = {"contentItem" = "\d+"}, options={"expose"=true})
     *
     * @param Request $request Current request instance
     * @param ContentItemEntity $contentItem
     *
     * @return JsonResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if item to be edited isn't found
     * @throws RuntimeException      Thrown if item type isn't found
     */
    public function displayEditingAction(Request $request, ContentItemEntity $contentItem = null)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->__('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }

        if (null === $contentItem) {
            throw new NotFoundHttpException($this->__('No such content found.'));
        }

        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->mayEdit($contentItem)) {
            throw new AccessDeniedException();
        }

        $contentTypeClass = $contentItem->getOwningType();
        $container = $this->get('service_container');
        if (!class_exists($contentTypeClass) || !$container->has($contentTypeClass)) {
            throw new RuntimeException($this->__('Invalid content type received.'));
        }

        $contentType = $container->get($contentTypeClass);
        $contentType->setEntity($contentItem);

        return $this->json([
            'title' => $this->getWidgetTitle($contentItem, $contentType),
            'content' => $contentType->display(true),
            'panelClass' => $this->getWidgetPanelClass($contentItem)
        ]);
    }

    /**
     * The zikulacontentmodule_widgetTitle filter displays the title for the widget
     * of a given content item entity.
     * Example:
     *     {{ contentItem|zikulacontentmodule_widgetTitle }}
     *
     * @param ContentItemEntity $item
     * @param ContentTypeInterface $contentType
     *
     * @return string Widget title
     */
    protected function getWidgetTitle(ContentItemEntity $item, ContentTypeInterface $contentType)
    {
        $icon = '<i class="fa fa-' . $contentType->getIcon() . '"></i>';
        $title = $contentType->getTitle();

        $translator = $this->get('translator.default');
        if (!$item->isCurrentlyActive()) {
            $title .= ' (' . $translator->__('inactive') . ')';
        } elseif ('1' != $item->getScope()) {
            $scope = $item->getScope();
            if ('0' == $scope) {
                $title .= ' (' . $translator->__('only users') . ')';
            } elseif ('2' == $scope) {
                $title .= ' (' . $translator->__('only guests') . ')';
            }
        } elseif (count($item->getStylingClasses()) > 0) {
            $title .= ' (' . $translator->__('has styles') . ')';
        }

        return $icon . ' ' . $title;
    }

    /**
     * The zikulacontentmodule_widgetPanelClass filter displays the name
     * of a bootstrap panel class for a given content item entity.
     * Example:
     *     {{ contentItem|zikulacontentmodule_widgetPanelClass }}
     *
     * @param ContentItemEntity $item
     *
     * @return string Widget panel class name
     */
    protected function getWidgetPanelClass(ContentItemEntity $item)
    {
        $result = 'primary';

        if (!$item->isCurrentlyActive()) {
            $result = 'danger';
        } elseif ('1' != $item->getScope()) {
            $scope = $item->getScope();
            if ('0' == $scope) {
                $result = 'success';
            } elseif ('2' == $scope) {
                $result = 'warning';
            }
        } elseif (count($item->getStylingClasses()) > 0) {
            $result = 'info';
        }

        return $result;
    }

    /**
     * This action provides a handling of edit requests for content items.
     *
     * @Route("/item/edit/{contentItem}", requirements = {"contentItem" = "\d+"}, options={"expose"=true})
     *
     * @param Request $request Current request instance
     * @param ContentItemEntity $contentItem
     *
     * @return JsonResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if item to be edited isn't found
     * @throws RuntimeException      Thrown if item type isn't found
     */
    public function editAction(Request $request, ContentItemEntity $contentItem = null)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->__('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }

        $contentTypeClass = null;
        $isCreation = false;

        $isPost = $request->isMethod('POST');
        $dataSource = $isPost ? $request->request : $request->query;

        // permission check
        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (null === $contentItem) {
            $isCreation = true;
            if (!$permissionHelper->hasComponentPermission('page', ACCESS_ADD)) {
                throw new AccessDeniedException();
            }
            $pageId = $dataSource->getInt('pageId', 0);
            $contentTypeClass = $request->query->get('type', '');
            if ($pageId < 1 || empty($contentTypeClass) || !class_exists($contentTypeClass)) {
                throw new RuntimeException($this->__('Invalid input received.'));
            }

            $factory = $this->get('zikula_content_module.entity_factory');
            $page = $factory->getRepository('page')->selectById($pageId);
            if (null === $page) {
                throw new NotFoundHttpException();
            }

            $contentItem = $factory->createContentItem();
            $contentItem->setOwningType($contentTypeClass);
        } else {
            if (!$permissionHelper->mayEdit($contentItem)) {
                throw new AccessDeniedException();
            }
            $contentTypeClass = $contentItem->getOwningType();
        }

        $container = $this->get('service_container');
        if (!$container->has($contentTypeClass)) {
            throw new RuntimeException($this->__('Invalid content type received.'));
        }

        $contentType = $container->get($contentTypeClass);
        if (true === $isCreation) {
            $contentItem->setContentData($contentType->getDefaultData());
        }

        $routeArgs = [];
        if ($isCreation) {
            $routeArgs = [
                'type' => $contentTypeClass
            ];
        } else {
            $routeArgs = [
                'contentItem' => $contentItem->getId()
            ];
        }
        $route = $this->get('router')->generate('zikulacontentmodule_contentitem_edit', $routeArgs);

        $form = $this->createForm(ContentItemType::class, $contentItem, [
            'action' => $route
        ]);
        $editFormClass = $contentType->getEditFormClass();
        if (null !== $editFormClass && '' !== $editFormClass && class_exists($editFormClass)) {
            $form->add('contentData', $editFormClass, $contentType->getEditFormOptions());
        }

        $templateParameters = [
            'mode' => (true === $isCreation ? 'create' : 'edit'),
            'contentItem' => $contentItem,
            'form' => $form->createView(),
            'contentType' => $contentType,
            'contentFormTemplate' => $contentType->getEditTemplatePath()
        ];

        if ($contentItem->supportsHookSubscribers()) {
            // Call form aware display hooks
            $hookHelper = $this->get('zikula_content_module.hook_helper');
            $formHook = $hookHelper->callFormDisplayHooks($form, $contentItem, FormAwareCategory::TYPE_EDIT);
            $templateParameters['formHookTemplates'] = $formHook->getTemplates();
        }

        if ($isPost) {
            $workflowHelper = $this->get('zikula_content_module.workflow_helper');
            $action = $request->request->get('action', '');
            if (!in_array($action, ['save', 'delete'])) {
                throw new RuntimeException($this->__('Invalid action received.'));
            }

            $form->handleRequest($request);
            //if ($form->isValid()) // TODO investigate
            if ('save' == $action) {
                if (true === $isCreation) {
                    $page->addContentItems($contentItem);
                }

                $formData = $dataSource->get('zikulacontentmodule_contentitem');
                if (!isset($formData['stylingClasses'])) {
                    $contentItem->setStylingClasses([]);
                }
                $workflowAction = $isCreation ? 'submit' : 'update';

                // execute the workflow action
                $success = $workflowHelper->executeAction($contentItem, $workflowAction);
                if (!$success) {
                    return $this->json(['message' => $this->__('Error! An error occured during content submission.')], Response::HTTP_BAD_REQUEST);
                }

                return $this->json(['id' => $contentItem->getId(), 'message' => $this->__('Done! Content saved!')]);
            }
            if ('delete' == $action) {
                // determine available workflow actions
                $actions = $workflowHelper->getActionsForObject($contentItem);
                if (false === $actions || !is_array($actions)) {
                    throw new \RuntimeException($this->__('Error! Could not determine workflow actions.'));
                }

                // check whether deletion is allowed
                $deleteActionId = 'delete';
                $deleteAllowed = false;
                foreach ($actions as $actionId => $action) {
                    if ($actionId != $deleteActionId) {
                        continue;
                    }
                    $deleteAllowed = true;
                    break;
                }
                if (!$deleteAllowed) {
                    return $this->json(['message' => $this->__('Error! It is not allowed to delete this content item.')], Response::HTTP_BAD_REQUEST);
                }

                // Let any ui hooks perform additional validation actions
                $validationErrors = $hookHelper->callValidationHooks($contentItem, UiHooksCategory::TYPE_VALIDATE_DELETE);
                if (count($validationErrors) > 0) {
                    return $this->json(['message' => implode(' ', $validationErrors)], Response::HTTP_BAD_REQUEST);
                }

                // execute the workflow action
                $success = $workflowHelper->executeAction($contentItem, $deleteActionId);

                // Call form aware processing hooks
                $hookHelper->callFormProcessHooks($form, $contentItem, FormAwareCategory::TYPE_PROCESS_DELETE);

                // Let any ui hooks know that we have deleted the «name.formatForDisplay»
                $hookHelper->callProcessHooks($contentItem, UiHooksCategory::TYPE_PROCESS_DELETE);

                if (!$success) {
                    return $this->json(['message' => $this->__('Error! An error occured during content deletion.')], Response::HTTP_BAD_REQUEST);
                }

                return $this->json(['message' => $this->__('Done! Content deleted!')]);
            }
        }

        $template = (!$isPost ? 'edit' : 'editFormBody') . '.html.twig';

        $output = [
            'form' => $this->renderView('@ZikulaContentModule/ContentItem/' . $template, $templateParameters),
            'assets' => $contentType->getAssets(ContentTypeInterface::CONTEXT_EDIT),
            'jsEntryPoint' => $contentType->getJsEntrypoint(ContentTypeInterface::CONTEXT_EDIT)
        ];

        if (!$isPost) {
            return $this->json($output);
        }

        $output['message'] = $this->__('Error! Please check your input.');

        return $this->json($output, Response::HTTP_BAD_REQUEST);
    }
}
