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

namespace Zikula\ContentModule\Controller\Base;

use RuntimeException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Component\SortableColumns\Column;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\Controller\AbstractController;
use Zikula\Core\RouteUrl;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * Page controller base class.
 */
abstract class AbstractPageController extends AbstractController
{
    /**
     * This is the default action handling the index admin area called without defining arguments.
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminIndexAction(Request $request)
    {
        return $this->indexInternal($request, true);
    }
    
    /**
     * This is the default action handling the index area called without defining arguments.
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function indexAction(Request $request)
    {
        return $this->indexInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminIndex() and index().
     */
    protected function indexInternal(Request $request, $isAdmin = false)
    {
        $objectType = 'page';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_OVERVIEW;
        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        return $this->redirectToRoute('zikulacontentmodule_page_' . $templateParameters['routeArea'] . 'view');
    }
    
    /**
     * This action provides an item list overview in the admin area.
     *
     * @param Request $request Current request instance
     * @param string $sort         Sorting field
     * @param string $sortdir      Sorting direction
     * @param int    $pos          Current pager position
     * @param int    $num          Amount of entries to display
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminViewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, true);
    }
    
    /**
     * This action provides an item list overview.
     *
     * @param Request $request Current request instance
     * @param string $sort         Sorting field
     * @param string $sortdir      Sorting direction
     * @param int    $pos          Current pager position
     * @param int    $num          Amount of entries to display
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function viewAction(Request $request, $sort, $sortdir, $pos, $num)
    {
        return $this->viewInternal($request, $sort, $sortdir, $pos, $num, false);
    }
    
    /**
     * This method includes the common implementation code for adminView() and view().
     */
    protected function viewInternal(Request $request, $sort, $sortdir, $pos, $num, $isAdmin = false)
    {
        $objectType = 'page';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        if (!$isAdmin && 'tree' == $request->query->getAlnum('tpl', '')) {
            $permLevel = ACCESS_EDIT;
        }
        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        $controllerHelper = $this->get('zikula_content_module.controller_helper');
        $viewHelper = $this->get('zikula_content_module.view_helper');
        
        // check if deleted entities should be displayed
        $viewDeleted = $request->query->getInt('deleted', 0);
        if ($viewDeleted == 1 && $permissionHelper->hasComponentPermission('page', ACCESS_EDIT)) {
            $templateParameters['deletedEntities'] = $this->get('zikula_content_module.loggable_helper')->getDeletedEntities($objectType);
        
            return $viewHelper->processTemplate($objectType, 'viewDeleted', $templateParameters);
        }
        
        $request->query->set('sort', $sort);
        $request->query->set('sortdir', $sortdir);
        $request->query->set('pos', $pos);
        
        $sortableColumns = new SortableColumns($this->get('router'), 'zikulacontentmodule_page_' . ($isAdmin ? 'admin' : '') . 'view', 'sort', 'sortdir');
        
        if ('tree' == $request->query->getAlnum('tpl', '')) {
            $templateParameters = $controllerHelper->processViewActionParameters($objectType, $sortableColumns, $templateParameters, true);
        
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
        
        $templateParameters = $controllerHelper->processViewActionParameters($objectType, $sortableColumns, $templateParameters, true);
        
        // filter by permissions
        $filteredEntities = [];
        foreach ($templateParameters['items'] as $page) {
            if (!$permissionHelper->hasEntityPermission($page, $permLevel)) {
                continue;
            }
            $filteredEntities[] = $page;
        }
        $templateParameters['items'] = $filteredEntities;
        
        // filter by category permissions
        $featureActivationHelper = $this->get('zikula_content_module.feature_activation_helper');
        if ($featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
            $templateParameters['items'] = $this->get('zikula_content_module.category_helper')->filterEntitiesByPermission($templateParameters['items']);
        }
        
        // check if there exist any deleted page
        $templateParameters['hasDeletedEntities'] = false;
        if ($permissionHelper->hasPermission(ACCESS_EDIT)) {
            $templateParameters['hasDeletedEntities'] = $this->get('zikula_content_module.loggable_helper')->hasDeletedEntities($objectType);
        }
        
        // fetch and return the appropriate template
        return $viewHelper->processTemplate($objectType, 'view', $templateParameters);
    }
    
    /**
     * This action provides a item detail view in the admin area.
     *
     * @param Request $request Current request instance
     * @param string $slug Slug of treated page instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be displayed isn't found
     */
    public function adminDisplayAction(Request $request, $slug)
    {
        $page = $this->get('zikula_content_module.entity_factory')->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }
    
        return $this->displayInternal($request, $page, true);
    }
    
    /**
     * This action provides a item detail view.
     *
     * @param Request $request Current request instance
     * @param string $slug Slug of treated page instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be displayed isn't found
     */
    public function displayAction(Request $request, $slug)
    {
        $page = $this->get('zikula_content_module.entity_factory')->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }
    
        return $this->displayInternal($request, $page, false);
    }
    
    /**
     * This method includes the common implementation code for adminDisplay() and display().
     */
    protected function displayInternal(Request $request, PageEntity $page, $isAdmin = false)
    {
        $objectType = 'page';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_READ;
        $route = $request->attributes->get('_route', '');
        if (!$isAdmin && 'zikulacontentmodule_page_displaydeleted' == $route) {
            $permLevel = ACCESS_EDIT;
        }
        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->hasEntityPermission($page, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $featureActivationHelper = $this->get('zikula_content_module.feature_activation_helper');
        if ($featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
            if (!$this->get('zikula_content_module.category_helper')->hasPermission($page)) {
                throw new AccessDeniedException();
            }
        }
        
        $requestedVersion = $request->query->getInt('version', 0);
        $versionPermLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        if ($requestedVersion > 0 && $permissionHelper->hasEntityPermission($page, $versionPermLevel)) {
            // preview of a specific version is desired, but detach entity
            $page = $this->get('zikula_content_module.loggable_helper')->revert($page, $requestedVersion, true);
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : '',
            $objectType => $page
        ];
        
        $controllerHelper = $this->get('zikula_content_module.controller_helper');
        $templateParameters = $controllerHelper->processDisplayActionParameters($objectType, $templateParameters, $page->supportsHookSubscribers());
        
        // fetch and return the appropriate template
        $response = $this->get('zikula_content_module.view_helper')->processTemplate($objectType, 'display', $templateParameters);
        
        return $response;
    }
    
    /**
     * This action provides a handling of edit requests in the admin area.
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function adminEditAction(Request $request)
    {
        return $this->editInternal($request, true);
    }
    
    /**
     * This action provides a handling of edit requests.
     *
     * @param Request $request Current request instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws RuntimeException      Thrown if another critical error occurs (e.g. workflow actions not available)
     */
    public function editAction(Request $request)
    {
        return $this->editInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminEdit() and edit().
     */
    protected function editInternal(Request $request, $isAdmin = false)
    {
        $objectType = 'page';
        // permission check
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        if (!$permissionHelper->hasComponentPermission($objectType, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $templateParameters = [
            'routeArea' => $isAdmin ? 'admin' : ''
        ];
        
        $controllerHelper = $this->get('zikula_content_module.controller_helper');
        $templateParameters = $controllerHelper->processEditActionParameters($objectType, $templateParameters);
        
        // delegate form processing to the form handler
        $formHandler = $this->get('zikula_content_module.form.handler.page');
        $result = $formHandler->processForm($templateParameters);
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        
        $templateParameters = $formHandler->getTemplateParameters();
        
        // fetch and return the appropriate template
        return $this->get('zikula_content_module.view_helper')->processTemplate($objectType, 'edit', $templateParameters);
    }
    
    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request Current request instance
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function adminHandleSelectedEntriesAction(Request $request)
    {
        return $this->handleSelectedEntriesActionInternal($request, true);
    }
    
    /**
     * Process status changes for multiple items.
     *
     * This function processes the items selected in the admin view page.
     * Multiple items may have their state changed or be deleted.
     *
     * @param Request $request Current request instance
     *
     * @return RedirectResponse
     *
     * @throws RuntimeException Thrown if executing the workflow action fails
     */
    public function handleSelectedEntriesAction(Request $request)
    {
        return $this->handleSelectedEntriesActionInternal($request, false);
    }
    
    /**
     * This method includes the common implementation code for adminHandleSelectedEntriesAction() and handleSelectedEntriesAction().
     *
     * @param Request $request Current request instance
     * @param boolean $isAdmin Whether the admin area is used or not
     */
    protected function handleSelectedEntriesActionInternal(Request $request, $isAdmin = false)
    {
        $objectType = 'page';
        
        // Get parameters
        $action = $request->request->get('action', null);
        $items = $request->request->get('items', null);
        
        $action = strtolower($action);
        
        $repository = $this->get('zikula_content_module.entity_factory')->getRepository($objectType);
        $workflowHelper = $this->get('zikula_content_module.workflow_helper');
        $hookHelper = $this->get('zikula_content_module.hook_helper');
        $logger = $this->get('logger');
        $userName = $this->get('zikula_users_module.current_user')->get('uname');
        
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
            if (!in_array($action, $actionIds)) {
                // action not allowed, skip this object
                continue;
            }
        
            if ($entity->supportsHookSubscribers()) {
                // Let any ui hooks perform additional validation actions
                $hookType = $action == 'delete' ? UiHooksCategory::TYPE_VALIDATE_DELETE : UiHooksCategory::TYPE_VALIDATE_EDIT;
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
            } catch (\Exception $exception) {
                $this->addFlash('error', $this->__f('Sorry, but an error occured during the %action% action.', ['%action%' => $action]) . '  ' . $exception->getMessage());
                $logger->error('{app}: User {user} tried to execute the {action} workflow action for the {entity} with id {id}, but failed. Error details: {errorMessage}.', ['app' => 'ZikulaContentModule', 'user' => $userName, 'action' => $action, 'entity' => 'page', 'id' => $itemId, 'errorMessage' => $exception->getMessage()]);
            }
        
            if (!$success) {
                continue;
            }
        
            if ($action == 'delete') {
                $this->addFlash('status', $this->__('Done! Item deleted.'));
                $logger->notice('{app}: User {user} deleted the {entity} with id {id}.', ['app' => 'ZikulaContentModule', 'user' => $userName, 'entity' => 'page', 'id' => $itemId]);
            } else {
                $this->addFlash('status', $this->__('Done! Item updated.'));
                $logger->notice('{app}: User {user} executed the {action} workflow action for the {entity} with id {id}.', ['app' => 'ZikulaContentModule', 'user' => $userName, 'action' => $action, 'entity' => 'page', 'id' => $itemId]);
            }
        
            if ($entity->supportsHookSubscribers()) {
                // Let any ui hooks know that we have updated or deleted an item
                $hookType = $action == 'delete' ? UiHooksCategory::TYPE_PROCESS_DELETE : UiHooksCategory::TYPE_PROCESS_EDIT;
                $url = null;
                if ($action != 'delete') {
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
     * @param Request $request Current request instance
     * @param integer $id      Identifier of entity
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be displayed isn't found
     */
    public function adminUndeleteAction(Request $request, $id = 0)
    {
        return $this->undeleteActionInternal($request, $id, true);
    }
    
    /**
     * Displays or undeletes a deleted page.
     *
     * @param Request $request Current request instance
     * @param integer $id      Identifier of entity
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be displayed isn't found
     */
    public function undeleteAction(Request $request, $id = 0)
    {
        return $this->undeleteActionInternal($request, $id, false);
    }
    
    /**
     * This method includes the common implementation code for adminUndeleteAction() and undeleteAction().
     *
     * @param Request $request Current request instance
     * @param integer $id      Identifier of page
     * @param boolean $isAdmin Whether the admin area is used or not
     */
    protected function undeleteActionInternal(Request $request, $id = 0, $isAdmin = false)
    {
        $page = $this->restoreDeletedEntity($id);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }
        
        $preview = $request->query->getInt('preview', 0);
        if ($preview == 1) {
            return $this->displayInternal($request, $page, $isAdmin);
        }
        
        try {
            $this->get('zikula_content_module.loggable_helper')->undelete($page);
            $this->addFlash('status', $this->__('Done! Undeleted page.'));
        } catch (\Exception $exception) {
            $this->addFlash('error', $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => 'undelete']) . '  ' . $exception->getMessage());
        }
        
        $this->get('zikula_content_module.translatable_helper')->refreshTranslationsFromLogData($page);
        
        $routeArea = $isAdmin ? 'admin' : '';
        
        return $this->redirectToRoute('zikulacontentmodule_page_' . $routeArea . 'display', $page->createUrlArgs());
    }
    
    /**
     * This method provides a change history for a given page.
     *
     * @param Request $request Current request instance
     * @param integer $slug    Identifier of page
     *
     * @return Response Output
     *
     * @throws NotFoundHttpException Thrown if invalid identifier is given or the page isn't found
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function adminLoggableHistoryAction(Request $request, $slug = '')
    {
        return $this->loggableHistoryActionInternal($request, $slug, true);
    }
    
    /**
     * This method provides a change history for a given page.
     *
     * @param Request $request Current request instance
     * @param integer $slug    Identifier of page
     *
     * @return Response Output
     *
     * @throws NotFoundHttpException Thrown if invalid identifier is given or the page isn't found
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function loggableHistoryAction(Request $request, $slug = '')
    {
        return $this->loggableHistoryActionInternal($request, $slug, false);
    }
    
    /**
     * This method includes the common implementation code for adminLoggableHistoryAction() and loggableHistoryAction().
     *
     * @param Request $request Current request instance
     * @param string  $slug    Identifier of page
     * @param boolean $isAdmin Whether the admin area is used or not
     */
    protected function loggableHistoryActionInternal(Request $request, $slug = '', $isAdmin = false)
    {
        if (empty($slug)) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }
        
        $entityFactory = $this->get('zikula_content_module.entity_factory');
        $page = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }
        
        $permissionHelper = $this->get('zikula_content_module.permission_helper');
        $permLevel = $isAdmin ? ACCESS_ADMIN : ACCESS_EDIT;
        if (!$permissionHelper->hasEntityPermission($page, $permLevel)) {
            throw new AccessDeniedException();
        }
        
        $routeArea = $isAdmin ? 'admin' : '';
        $entityManager = $entityFactory->getObjectManager();
        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:PageLogEntryEntity');
        $logEntries = $logEntriesRepository->getLogEntries($page);
        
        $revertToVersion = $request->query->getInt('revert', 0);
        if ($revertToVersion > 0 && count($logEntries) > 1) {
            // revert to requested version
            $page = $this->get('zikula_content_module.loggable_helper')->revert($page, $revertToVersion);
        
            try {
                // execute the workflow action
                $workflowHelper = $this->get('zikula_content_module.workflow_helper');
                $success = $workflowHelper->executeAction($page, 'update');
        
                $this->get('zikula_content_module.translatable_helper')->refreshTranslationsFromLogData($page);
        
                if ($success) {
                    $this->addFlash('status', $this->__f('Done! Reverted page to version %version%.', ['%version%' => $revertToVersion]));
                } else {
                    $this->addFlash('error', $this->__f('Error! Reverting page to version %version% failed.', ['%version%' => $revertToVersion]));
                }
            } catch (\Exception $exception) {
                $this->addFlash('error', $this->__f('Sorry, but an error occured during the %action% action. Please apply the changes again!', ['%action%' => 'update']) . '  ' . $exception->getMessage());
            }
        
            return $this->redirectToRoute('zikulacontentmodule_page_' . $routeArea . 'loggablehistory', ['slug' => $page['slug']]);
        }
        
        $isDiffView = false;
        $versions = $request->query->get('versions', []);
        if (is_array($versions) && count($versions) == 2) {
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
            list ($minVersion, $maxVersion, $diffValues) = $this->get('zikula_content_module.loggable_helper')->determineDiffViewParameters($logEntries, $versions);
            $templateParameters['minVersion'] = $minVersion;
            $templateParameters['maxVersion'] = $maxVersion;
            $templateParameters['diffValues'] = $diffValues;
        }
        
        return $this->render('@ZikulaContentModule/Page/history.html.twig', $templateParameters);
    }
    
}
