<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Controller\Base;

use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\CoreBundle\Controller\AbstractController;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Component\SortableColumns\Column;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Bundle\CoreBundle\RouteUrl;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Form\Handler\Page\EditHandler;
use Zikula\ContentModule\Helper\ControllerHelper;
use Zikula\ContentModule\Helper\HookHelper;
use Zikula\ContentModule\Helper\LoggableHelper;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ContentModule\Helper\TranslatableHelper;
use Zikula\ContentModule\Helper\ViewHelper;
use Zikula\ContentModule\Helper\WorkflowHelper;

/**
 * Page controller base class.
 */
abstract class AbstractPageController extends AbstractController
{
    
    /**
     * This is the default action handling the index area called without defining arguments.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    protected function indexInternal(
        Request $request,
        PermissionHelper $permissionHelper,
        bool $isAdmin = false
    ): Response {
        $objectType = 'page';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        return $this->redirectToRoute('zikulacontentmodule_page_' . $templateParameters['routeArea'] . 'view');
    }
    
    
    /**
     * This action provides an item list overview.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws Exception
     */
    protected function viewInternal(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        LoggableHelper $loggableHelper,
        string $sort,
        string $sortdir,
        int $page,
        int $num,
        bool $isAdmin = false
    ): Response {
        $objectType = 'page';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        if (!$isAdmin && 'tree' === $request->query->getAlnum('tpl')) {
            $permLevel = ACCESS_EDIT;
        }
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        // check if deleted entities should be displayed
        $viewDeleted = $request->query->getInt('deleted');
        if (1 === $viewDeleted && $permissionHelper->hasComponentPermission('page', ACCESS_EDIT)) {
            $templateParameters['deletedEntities'] = $loggableHelper->getDeletedEntities($objectType);
        
            return $viewHelper->processTemplate($objectType, 'viewDeleted', $templateParameters);
        }
        
        $request->query->set('sort', $sort);
        $request->query->set('sortdir', $sortdir);
        $request->query->set('page', $page);
        
        $routeName = 'zikulacontentmodule_page_' . ($isAdmin ? 'admin' : '') . 'view';
        $sortableColumns = new SortableColumns($router, $routeName, 'sort', 'sortdir');
        
        if ('tree' === $request->query->getAlnum('tpl')) {
            $templateParameters = $controllerHelper->processViewActionParameters(
                $objectType,
                $sortableColumns,
                $templateParameters,
                true
            );
        
            // fetch and return the appropriate template
            return $viewHelper->processTemplate($objectType, 'view', $templateParameters);
        }
        
        $sortableColumns->addColumns([
            new Column('workflowState'),
            new Column('title'),
            new Column('views'),
            new Column('active'),
            new Column('activeFrom'),
            new Column('activeTo'),
            new Column('inMenu'),
            new Column('optionalString1'),
            new Column('optionalString2'),
            new Column('currentVersion'),
            new Column('createdBy'),
            new Column('createdDate'),
            new Column('updatedBy'),
            new Column('updatedDate'),
        ]);
        
        $templateParameters = $controllerHelper->processViewActionParameters(
            $objectType,
            $sortableColumns,
            $templateParameters,
            true
        );
        
        // filter by permissions
        $templateParameters['items'] = $permissionHelper->filterCollection(
            $objectType,
            $templateParameters['items'],
            $permLevel
        );
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($objectType, 'view', $templateParameters);
    }
    
    
    /**
     * This action provides a item detail view.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be displayed isn't found
     */
    protected function displayInternal(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        PageEntity $page = null,
        string $slug = '',
        bool $isAdmin = false
    ): Response {
        if (null === $page) {
            $page = $entityFactory->getRepository('page')->selectBySlug($slug);
        }
        if (null === $page) {
            throw new NotFoundHttpException(
                $this->trans(
                    'No such page found.',
                    [],
                    'page'
                )
            );
        }
        
        $objectType = 'page';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        $route = $request->attributes->get('_route', '');
        if (!$isAdmin && 'zikulacontentmodule_page_displaydeleted' === $route) {
            $permLevel = ACCESS_EDIT;
        }
        if (!$permissionHelper->hasEntityPermission($page, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $requestedVersion = $request->query->getInt('version');
        $versionPermLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        if (0 < $requestedVersion && $permissionHelper->hasEntityPermission($page, $versionPermLevel)) {
            // preview of a specific version is desired, but detach entity
            $page = $loggableHelper->revert($page, $requestedVersion, true);
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
            $objectType => $page
        ];
        
        $templateParameters = $controllerHelper->processDisplayActionParameters(
            $objectType,
            $templateParameters,
            $page->supportsHookSubscribers()
        );
        
        // fetch and return the appropriate template
        $response = $viewHelper->processTemplate($objectType, 'display', $templateParameters);
        
        return $response;
    }
    
    
    /**
     * This action provides a handling of edit requests.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws RuntimeException Thrown if another critical error occurs (e.g. workflow actions not available)
     * @throws Exception
     */
    protected function editInternal(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EditHandler $formHandler,
        bool $isAdmin = false
    ): Response {
        $objectType = 'page';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        $templateParameters = $controllerHelper->processEditActionParameters($objectType, $templateParameters);
        
        // delegate form processing to the form handler
        $result = $formHandler->processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        
        $templateParameters = $formHandler->getTemplateParameters();
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($objectType, 'edit', $templateParameters);
    }
    
    
    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    protected function handleSelectedEntriesActionInternal(
        Request $request,
        LoggerInterface $logger,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        CurrentUserApiInterface $currentUserApi,
        bool $isAdmin = false
    ): RedirectResponse {
        $objectType = 'page';
        
        // Get parameters
        $action = $request->request->get('action');
        $items = $request->request->get('items');
        if (!is_array($items) || !count($items)) {
            return $this->redirectToRoute('zikulacontentmodule_page_' . ($isAdmin ? 'admin' : '') . 'index');
        }
        
        $action = strtolower($action);
        
        $repository = $entityFactory->getRepository($objectType);
        $userName = $currentUserApi->get('uname');
        
        // process each item
        foreach ($items as $itemId) {
            // check if item exists, and get record instance
            $entity = $repository->selectById($itemId, false);
            if (null === $entity) {
                continue;
            }
        
            // check if $action can be applied to this entity (may depend on it's current workflow state)
            $allowedActions = $workflowHelper->getActionsForObject($entity);
            $actionIds = array_keys($allowedActions);
            if (!in_array($action, $actionIds, true)) {
                // action not allowed, skip this object
                continue;
            }
        
            if ($entity->supportsHookSubscribers()) {
                // Let any ui hooks perform additional validation actions
                $hookType = 'delete' === $action
                    ? UiHooksCategory::TYPE_VALIDATE_DELETE
                    : UiHooksCategory::TYPE_VALIDATE_EDIT
                ;
                $validationErrors = $hookHelper->callValidationHooks($entity, $hookType);
                if (count($validationErrors) > 0) {
                    foreach ($validationErrors as $message) {
                        $this->addFlash('error', $message);
                    }
                    continue;
                }
            }
        
            $success = false;
            try {
                // execute the workflow action
                $success = $workflowHelper->executeAction($entity, $action);
            } catch (Exception $exception) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'Sorry, but an error occured during the %action% action.',
                        ['%action%' => $action]
                    ) . '  ' . $exception->getMessage()
                );
                $logger->error(
                    '{app}: User {user} tried to execute the {action} workflow action for the {entity} with id {id},'
                        . ' but failed. Error details: {errorMessage}.',
                    [
                        'app' => 'ZikulaContentModule',
                        'user' => $userName,
                        'action' => $action,
                        'entity' => 'page',
                        'id' => $itemId,
                        'errorMessage' => $exception->getMessage()
                    ]
                );
            }
        
            if (!$success) {
                continue;
            }
        
            if ('delete' === $action) {
                $this->addFlash(
                    'status',
                    $this->trans(
                        'Done! Page deleted.',
                        [],
                        'page'
                    )
                );
                $logger->notice(
                    '{app}: User {user} deleted the {entity} with id {id}.',
                    [
                        'app' => 'ZikulaContentModule',
                        'user' => $userName,
                        'entity' => 'page',
                        'id' => $itemId
                    ]
                );
            } else {
                $this->addFlash(
                    'status',
                    $this->trans(
                        'Done! Page updated.',
                        [],
                        'page'
                    )
                );
                $logger->notice(
                    '{app}: User {user} executed the {action} workflow action for the {entity} with id {id}.',
                    [
                        'app' => 'ZikulaContentModule',
                        'user' => $userName,
                        'action' => $action,
                        'entity' => 'page',
                        'id' => $itemId
                    ]
                );
            }
        
            if ($entity->supportsHookSubscribers()) {
                // Let any ui hooks know that we have updated or deleted an item
                $hookType = 'delete' === $action
                    ? UiHooksCategory::TYPE_PROCESS_DELETE
                    : UiHooksCategory::TYPE_PROCESS_EDIT
                ;
                $url = null;
                if ('delete' !== $action) {
                    $urlArgs = $entity->createUrlArgs();
                    $urlArgs['_locale'] = $request->getLocale();
                    $url = new RouteUrl('zikulacontentmodule_page_display', $urlArgs);
                }
                $hookHelper->callProcessHooks($entity, $hookType, $url);
            }
        }
        
        return $this->redirectToRoute('zikulacontentmodule_page_' . ($isAdmin ? 'admin' : '') . 'index');
    }
    
    
    /**
     * Displays or undeletes a deleted page.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be displayed isn't found
     */
    protected function undeleteActionInternal(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        CategoryHelper $categoryHelper,
        FeatureActivationHelper $featureActivationHelper,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        int $id = 0,
        bool $isAdmin = false
    ): Response {
        $page = $loggableHelper->restoreDeletedEntity('page', $id);
        if (null === $page) {
            throw new NotFoundHttpException(
                $this->trans(
                    'No such page found.',
                    [],
                    'page'
                )
            );
        }
        
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        if (!$permissionHelper->hasEntityPermission($page, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $preview = $request->query->getInt('preview');
        if (1 === $preview) {
            return $this->displayInternal(
                $request,
                $permissionHelper,
                $controllerHelper,
                $viewHelper,
                $entityFactory,
                $categoryHelper,
                $featureActivationHelper,
                $loggableHelper,
                $page,
                null,
                $isAdmin
            );
        }
        
        try {
            $loggableHelper->undelete($page);
            $this->addFlash(
                'status',
                $this->trans(
                    'Done! Page undeleted.',
                    [],
                    'page'
                )
            );
        } catch (Exception $exception) {
            $this->addFlash(
                'error',
                $this->trans(
                    'Sorry, but an error occured during the %action% action. Please apply the changes again!',
                    ['%action%' => 'undelete']
                ) . '  ' . $exception->getMessage()
            );
        }
        
        $translatableHelper->refreshTranslationsFromLogData($page);
        
        $routeArea = $isAdmin ? 'admin' : '';
        
        return $this->redirectToRoute('zikulacontentmodule_page_' . $routeArea . 'display', $page->createUrlArgs());
    }
    
    
    /**
     * This method provides a change history for a given page.
     *
     * @throws NotFoundHttpException Thrown if invalid identifier is given or the page isn't found
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    protected function loggableHistoryActionInternal(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        string $slug = '',
        bool $isAdmin = false
    ): Response {
        if (empty($slug)) {
            throw new NotFoundHttpException(
                $this->trans(
                    'No such page found.',
                    [],
                    'page'
                )
            );
        }
        
        $page = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException(
                $this->trans(
                    'No such page found.',
                    [],
                    'page'
                )
            );
        }
        
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        if (!$permissionHelper->hasEntityPermission($page, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $routeArea = $isAdmin ? 'admin' : '';
        $entityManager = $entityFactory->getEntityManager();
        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:PageLogEntryEntity');
        $logEntries = $logEntriesRepository->getLogEntries($page);
        
        $revertToVersion = $request->query->getInt('revert');
        if (0 < $revertToVersion && 1 < count($logEntries)) {
            // revert to requested version
            $pageId = $page->getId();
            $page = $loggableHelper->revert($page, $revertToVersion);
        
            try {
                // execute the workflow action
                $success = $workflowHelper->executeAction($page, 'update');
        
                $translatableHelper->refreshTranslationsFromLogData($page);
        
                if ($success) {
                    $this->addFlash(
                        'status',
                        $this->trans(
                            'Done! Reverted page to version %version%.',
                            ['%version%' => $revertToVersion],
                            'page'
                        )
                    );
                } else {
                    $this->addFlash(
                        'error',
                        $this->trans(
                            'Error! Reverting page to version %version% failed.',
                            ['%version%' => $revertToVersion],
                            'page'
                        )
                    );
                }
            } catch (Exception $exception) {
                $this->addFlash(
                    'error',
                    $this->trans(
                        'Sorry, but an error occured during the %action% action. Please apply the changes again!',
                        ['%action%' => 'update']
                    ) . '  ' . $exception->getMessage()
                );
            }
        
            $page = $entityFactory->getRepository('page')->selectById($pageId);
        
            return $this->redirectToRoute(
                'zikulacontentmodule_page_' . $routeArea . 'loggablehistory',
                ['slug' => $page['slug']]
            );
        }
        
        $isDiffView = false;
        $versions = $request->query->get('versions', []);
        if (is_array($versions) && 2 === count($versions)) {
            $isDiffView = true;
            $allVersionsExist = true;
            foreach ($versions as $versionNumber) {
                $versionExists = false;
                foreach ($logEntries as $logEntry) {
                    if ($versionNumber == $logEntry->getVersion()) {
                        $versionExists = true;
                        break;
                    }
                }
                if (!$versionExists) {
                    $allVersionsExist = false;
                    break;
                }
            }
            if (!$allVersionsExist) {
                $isDiffView = false;
            }
        }
        
        $templateParameters = [
            'routeArea' => $routeArea,
            'page' => $page,
            'logEntries' => $logEntries,
            'isDiffView' => $isDiffView
        ];
        
        if (true === $isDiffView) {
            list (
                $minVersion,
                $maxVersion,
                $diffValues
            ) = $loggableHelper->determineDiffViewParameters(
                $logEntries,
                $versions
            );
            $templateParameters['minVersion'] = $minVersion;
            $templateParameters['maxVersion'] = $maxVersion;
            $templateParameters['diffValues'] = $diffValues;
        }
        
        return $this->render('@ZikulaContentModule/Page/history.html.twig', $templateParameters);
    }
}
