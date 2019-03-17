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

use Zikula\ContentModule\Controller\Base\AbstractPageController;

use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\ThemeModule\Engine\Annotation\Theme;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Form\Handler\Page\EditHandler;
use Zikula\ContentModule\Form\Type\TranslateType;
use Zikula\ContentModule\Helper\CategoryHelper;
use Zikula\ContentModule\Helper\ControllerHelper;
use Zikula\ContentModule\Helper\ContentDisplayHelper;
use Zikula\ContentModule\Helper\FeatureActivationHelper;
use Zikula\ContentModule\Helper\HookHelper;
use Zikula\ContentModule\Helper\LoggableHelper;
use Zikula\ContentModule\Helper\ModelHelper;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ContentModule\Helper\TranslatableHelper;
use Zikula\ContentModule\Helper\ViewHelper;
use Zikula\ContentModule\Helper\WorkflowHelper;

/**
 * Page controller class providing navigation and interaction functionality.
 */
class PageController extends AbstractPageController
{
    /**
     * @inheritDoc
     *
     * @Route("/admin/pages",
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminIndexAction(Request $request, PermissionHelper $permissionHelper)
    {
        return $this->indexInternal($request, $permissionHelper, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/pages",
     *        methods = {"GET"}
     * )
     */
    public function indexAction(Request $request, PermissionHelper $permissionHelper)
    {
        // permission check
        $permLevel = ACCESS_READ;
        if (!$permissionHelper->hasComponentPermission('page', ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        return $this->redirectToRoute('zikulacontentmodule_page_sitemap');
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/admin/pages/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminViewAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        CategoryHelper $categoryHelper,
        FeatureActivationHelper $featureActivationHelper,
        LoggableHelper $loggableHelper,
        $sort,
        $sortdir,
        $pos,
        $num
    ) {
        return $this->viewInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $categoryHelper, $featureActivationHelper, $loggableHelper, $sort, $sortdir, $pos, $num, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/pages/view/{sort}/{sortdir}/{pos}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "pos" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "pos" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function viewAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        CategoryHelper $categoryHelper,
        FeatureActivationHelper $featureActivationHelper,
        LoggableHelper $loggableHelper,
        $sort,
        $sortdir,
        $pos,
        $num
    ) {
        return $this->viewInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $categoryHelper, $featureActivationHelper, $loggableHelper, $sort, $sortdir, $pos, $num, false);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/admin/page/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"},
     *        options={"expose"=true}
     * )
     * @Theme("admin")
     */
    public function adminEditAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EditHandler $formHandler
    ) {
        return $this->editInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $formHandler, true);
    }
    
    /**
     * @inheritDoc
     *
     * @Route("/page/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"},
     *        options={"expose"=true}
     * )
     */
    public function editAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EditHandler $formHandler
    ) {
        return $this->editInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $formHandler, false);
    }
    
    /**
     * @inheritDoc
     * @Route("/admin/page/deleted/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminUndeleteAction(
        Request $request,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        $id = 0
    ) {
        return $this->undeleteActionInternal($request, $loggableHelper, $translatableHelper, $id, true);
    }
    
    /**
     * @inheritDoc
     * @Route("/page/deleted/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function undeleteAction(
        Request $request,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        $id = 0
    ) {
        return $this->undeleteActionInternal($request, $loggableHelper, $translatableHelper, $id, false);
    }
    
    /**
     * @inheritDoc
     * @Route("/admin/page/history/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminLoggableHistoryAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        $slug = ''
    ) {
        return $this->loggableHistoryActionInternal($request, $permissionHelper, $entityFactory, $loggableHelper, $translatableHelper, $workflowHelper, $slug, true);
    }
    
    /**
     * @inheritDoc
     * @Route("/page/history/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     */
    public function loggableHistoryAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        $slug = ''
    ) {
        return $this->loggableHistoryActionInternal($request, $permissionHelper, $entityFactory, $loggableHelper, $translatableHelper, $workflowHelper, $slug, false);
    }
    
    /**
     * Handles management of content items for a given page.
     *
     * @Route("/admin/page/manageContent/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     * @Template("ZikulaContentModule:Page:manageContent.html.twig")
     *
     * @param Request $request
     * @param string $slug Slug of treated page instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    public function adminManageContentAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        $slug
    ) {
        return $this->manageContentInternal($request, $permissionHelper, $entityFactory, $slug, true);
    }

    /**
     * Handles management of content items for a given page.
     *
     * @Route("/page/manageContent/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     * @Template("ZikulaContentModule:Page:manageContent.html.twig")
     *
     * @param Request $request
     * @param string $slug Slug of treated page instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    public function manageContentAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        $slug
    ) {
        return $this->manageContentInternal($request, $permissionHelper, $entityFactory, $slug, false);
    }

    /**
     * This method includes the common implementation code for adminManageContentAction() and manageContentAction().
     *
     * @param Request $request
     * @param PermissionHelper $permissionHelper
     * @param EntityFactory $entityFactory
     * @param string $slug Slug of treated page instance
     * @param boolean $isAdmin Whether the admin area is used or not
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    protected function manageContentInternal(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        $slug,
        $isAdmin = false
    ) {
        $page = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }

        if (!$permissionHelper->mayManagePageContent($page)) {
            throw new AccessDeniedException();
        }

        $routeArea = $isAdmin ? 'admin' : '';

        // detect return url
        $routePrefix = 'zikulacontentmodule_page_' . $routeArea;
        $returnUrl = $this->get('router')->generate($routePrefix . 'display', $page->createUrlArgs());
        if ($request->headers->has('referer')) {
            $currentReferer = $request->headers->get('referer');
            if ($currentReferer != $request->getUri()) {
                $returnUrl = $currentReferer;
            }
        }

        // try to guarantee that only one person at a time can be editing this entity
        $hasPageLockModule = $this->get('kernel')->isBundle('ZikulaPageLockModule');
        if (true === $hasPageLockModule) {
            $lockingApi = $this->get('Zikula\PageLockModule\Api\LockingApi');
            $lockName = 'ZikulaContentModulePageContent' . $page->getKey();

            $lockingApi->addLock($lockName, $returnUrl);
        }

        $sectionStyles = $this->getVar('sectionStyles', '');
        $sectionStyleChoices = [];
        $userClasses = explode("\n", $sectionStyles);
        foreach ($userClasses as $class) {
            list($value, $text) = explode('|', $class);
            $value = trim($value);
            $text = trim($text);
            if (!empty($text) && !empty($value)) {
                $sectionStyleChoices[$text] = $value;
            }
        }

        return [
            'routeArea' => $routeArea,
            'page' => $page,
            'returnUrl' => $returnUrl,
            'sectionStyles' => $sectionStyleChoices
        ];
    }

    /**
     * Saves the layout data for a given page.
     *
     * @Route("/page/updateLayout/{id}",
     *        requirements = {"id" = "\d+"},
     *        defaults = {"id" = "0"},
     *        methods = {"POST"},
     *        options={"expose"=true}
     * )
     *
     * @param Request $request
     * @param PermissionHelper $permissionHelper
     * @param EntityFactory $entityFactory
     * @param WorkflowHelper $workflowHelper
     * @param integer $id Identifier of treated page instance
     *
     * @return JsonResponse Output
     *
     * @throws NotFoundHttpException Thrown if the page was not found
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function updateLayoutAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        $id
    ) {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->__('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }

        $page = $entityFactory->getRepository('page')->selectById($id);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }

        if (!$permissionHelper->mayManagePageContent($page)) {
            throw new AccessDeniedException();
        }

        $layoutData = $request->request->get('layoutData', []);
        $page->setLayout($layoutData);

        $page->set_actionDescriptionForLogEntry('_HISTORY_PAGE_LAYOUT_CHANGED');

        // no hook calls on purpose here, because layout data should not be of interest for other modules

        $success = $workflowHelper->executeAction($page, 'update');
        if (!$success) {
            return $this->json(['message' => $this->__('Error! An error occured during layout persistence.')], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => $this->__('Done! Layout saved.')]);
    }

    /**
     * Displays a sitemap.
     *
     * @Route("/sitemap.{_format}",
     *        requirements = {"_format" = "html|xml"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     *
     * @param Request $request
     * @param PermissionHelper $permissionHelper
     * @param EntityFactory $entityFactory
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function sitemapAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory
    ) {
        // permission check
        if (!$permissionHelper->hasComponentPermission('page', ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        $ignoreFirstTreeLevel = $this->getVar('ignoreFirstTreeLevelInRoutes', true);
        $where = 'tbl.lvl = ' . ($ignoreFirstTreeLevel ? '1' : '0');
        $rootPages = $entityFactory->getRepository('page')->selectWhere($where, 'tbl.lft');

        return $this->render('@ZikulaContentModule/Page/sitemap.' . $request->getRequestFormat() . '.twig', [
            'pages' => $rootPages
        ]);
    }
    
    /**
     * @Route("/admin/page/duplicate/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     */
    public function adminDuplicateAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        ModelHelper $modelHelper,
        HookHelper $hookHelper,
        $slug
    ) {
        return $this->duplicateInternal($request, $permissionHelper, $entityFactory, $workflowHelper, $modelHelper, $hookHelper, $slug, true);
    }

    /**
     * @Route("/page/duplicate/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     */
    public function duplicateAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        ModelHelper $modelHelper,
        HookHelper $hookHelper,
        $slug
    ) {
        return $this->duplicateInternal($request, $permissionHelper, $entityFactory, $workflowHelper, $modelHelper, $hookHelper, $slug, false);
    }

    /**
     * Handles duplication of a given page.
     *
     * @param Request $request
     * @param PermissionHelper $permissionHelper
     * @param EntityFactory $entityFactory
     * @param string $slug Slug of treated page instance
     * @param boolean $isAdmin Whether the admin area is used or not
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be duplicated isn't found
     */
    protected function duplicateInternal(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        ModelHelper $modelHelper,
        HookHelper $hookHelper,
        $slug,
        $isAdmin = false
    ) {
        $oldPage = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $oldPage) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }

        if (!$permissionHelper->mayEdit($oldPage)) {
            throw new AccessDeniedException();
        }

        $routeArea = $isAdmin ? 'admin' : '';

        // detect return url
        $routePrefix = 'zikulacontentmodule_page_' . $routeArea;
        $returnUrl = $this->get('router')->generate($routePrefix . 'view');
        if ($request->headers->has('referer')) {
            $currentReferer = $request->headers->get('referer');
            if ($currentReferer != $request->getUri()) {
                $returnUrl = $currentReferer;
            }
        }

        $titleSuffix = ' - ' . $this->__('new copy');
        $newPage = clone $oldPage;
        $newPage->setTitle($newPage->getTitle() . $titleSuffix);
        $slugParts = explode('/', $newPage->getSlug());
        $newPage->setSlug(end($slugParts) . str_replace(' ', '-', $titleSuffix));

        if ($newPage->supportsHookSubscribers()) {
            // Let any ui hooks perform additional validation actions
            $validationErrors = $hookHelper->callValidationHooks($newPage, UiHooksCategory::TYPE_VALIDATE_EDIT);
            if (count($validationErrors) > 0) {
                $this->addFlash('error', implode(' ', $validationErrors));

                return $this->redirect($returnUrl);
            }
        }

        $newPage->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CLONED|%page=' . $oldPage->getKey());

        $success = $workflowHelper->executeAction($newPage, 'submit');
        if (!$success) {
            $this->addFlash('error', $this->__('Error! An error occured during duplicating the page.'));

            return $this->redirect($returnUrl);
        }

        $modelHelper->clonePageTranslations($oldPage->getId(), $newPage->getId(), $titleSuffix);

        $layoutData = $newPage->getLayout();
        foreach ($oldPage->getContentItems() as $item) {
            $newItem = clone $item;
            $newPage->addContentItems($newItem);

            if ($newItem->supportsHookSubscribers()) {
                // Let any ui hooks perform additional validation actions
                $validationErrors = $hookHelper->callValidationHooks($newItem, UiHooksCategory::TYPE_VALIDATE_EDIT);
                if (count($validationErrors) > 0) {
                    $this->addFlash('error', implode(' ', $validationErrors));

                    continue;
                }
            }
            $success = $workflowHelper->executeAction($newItem, 'submit');
            if (!$success) {
                $this->addFlash('error', $this->__('Error! An error occured during duplicating the page.'));

                continue;
            }

            $modelHelper->cloneContentTranslations($item->getId(), $newItem->getId());

            if ($newItem->supportsHookSubscribers()) {
                // Let any ui hooks know that we have updated the content item
                $hookHelper->callProcessHooks($newItem, UiHooksCategory::TYPE_PROCESS_EDIT);
            }

            if (is_array($layoutData) && count($layoutData) > 0) {
                $oldItemId = $item->getId();
                $newItemId = $newItem->getId();
                foreach ($layoutData as $sectionKey => $section) {
                    if (!isset($section['widgets']) || !is_array($section['widgets']) || !count($section['widgets'])) {
                        continue;
                    }
                    foreach ($section['widgets'] as $widgetKey => $widget) {
                        if ($widget['id'] != $oldItemId) {
                            continue;
                        }
                        $layoutData[$sectionKey]['widgets'][$widgetKey]['id'] = $newItemId;
                        break 2;
                    }
                }
            }
        }
        $newPage->setLayout($layoutData);

        $success = $workflowHelper->executeAction($newPage, 'update');
        if (!$success) {
            $this->addFlash('error', $this->__('Error! An error occured during duplicating the page.'));

            return $this->redirect($returnUrl);
        }

        $this->addFlash('success', $this->__('Done! Page duplicated.'));

        if ($newPage->supportsHookSubscribers()) {
            // Let any ui hooks know that we have updated the page
            $hookHelper->callProcessHooks($newPage, UiHooksCategory::TYPE_PROCESS_EDIT);
        }

        return $this->redirect($returnUrl);
    }

    /**
     * @Route("/admin/page/translate/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET", "POST"},
     *        options={"expose"=true}
     * )
     * @Template("ZikulaContentModule:Page:translate.html.twig")
     */
    public function adminTranslateAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        ContentDisplayHelper $contentDisplayHelper,
        $slug
    ) {
        return $this->translateInternal($request, $permissionHelper, $entityFactory, $loggableHelper, $translatableHelper, $workflowHelper, $contentDisplayHelper, $slug, true);
    }

    /**
     * @Route("/page/translate/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET", "POST"},
     *        options={"expose"=true}
     * )
     * @Template("ZikulaContentModule:Page:translate.html.twig")
     */
    public function translateAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        ContentDisplayHelper $contentDisplayHelper,
        $slug
    ) {
        return $this->translateInternal($request, $permissionHelper, $entityFactory, $loggableHelper, $translatableHelper, $workflowHelper, $contentDisplayHelper, $slug, false);
    }

    /**
     * Handles page translation.
     *
     * @param Request $request
     * @param PermissionHelper $permissionHelper
     * @param EntityFactory $entityFactory
     * @param LoggableHelper $loggableHelper
     * @param TranslatableHelper $translatableHelper
     * @param WorkflowHelper $workflowHelper
     * @param ContentDisplayHelper $contentDisplayHelper
     * @param string $slug Slug of treated page instance
     * @param boolean $isAdmin Whether the admin area is used or not
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    protected function translateInternal(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        ContentDisplayHelper $contentDisplayHelper,
        $slug,
        $isAdmin = false
    ) {
        $page = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }

        if (!$permissionHelper->mayEdit($page)) {
            throw new AccessDeniedException();
        }
        if (!$permissionHelper->mayManagePageContent($page)) {
            throw new AccessDeniedException();
        }

        $contentItemId = $request->query->getInt('cid', 0);
        $contentItem = null;
        if ($contentItemId > 0) {
            foreach ($page->getContentItems() as $pageContentItem) {
                if ($contentItemId != $pageContentItem->getId()) {
                    continue;
                }
                $contentItem = $pageContentItem;
                break;
            }
            if (null === $contentItem) {
                throw new NotFoundHttpException($this->__('No such content found.'));
            }
        }

        $routeArea = $isAdmin ? 'admin' : '';

        // detect return url
        $routePrefix = 'zikulacontentmodule_page_' . $routeArea;
        $returnUrl = $this->get('router')->generate($routePrefix . 'display', $page->createUrlArgs());

        // try to guarantee that only one person at a time can be editing this entity
        $hasPageLockModule = $this->get('kernel')->isBundle('ZikulaPageLockModule');
        if (true === $hasPageLockModule) {
            $lockingApi = $this->get('Zikula\PageLockModule\Api\LockingApi');
            $lockName = 'ZikulaContentModuleTranslatePage' . $page->getKey();

            $lockingApi->addLock($lockName, $returnUrl);
        }

        $supportedLanguages = $translatableHelper->getSupportedLanguages('page');

        $isPageStep = null === $contentItem;
        $currentStep = 1;
        $translationInfo = $translatableHelper->getTranslationInfo($page, $contentItem);

        $formObject = $isPageStep ? $page : $contentItem;
        $formOptions = [
            'mode' => ($isPageStep ? 'page' : 'item'),
            'translations' => []
        ];

        $translations = $translatableHelper->prepareEntityForEditing($formObject);
        foreach ($translations as $language => $translationData) {
            $formOptions['translations'][$language] = $translationData;
        }

        $pageSlug = $page->getSlug();
        $contentType = null;
        if ($isPageStep) {
            $slugParts = explode('/', $pageSlug);
            $page->setSlug(end($slugParts));
        } else {
            $contentType = $contentDisplayHelper->initContentType($contentItem);
            foreach ($translationInfo['items'] as $item) {
                if ($item->getEntity()->getId() != $contentItemId) {
                    continue;
                }
                $currentStep++;
                break;
            }

            $formOptions['content_type'] = $contentType;
            $contentDisplayHelper->prepareForDisplay($contentItem, ContentTypeInterface::CONTEXT_TRANSLATION);
        }
        $form = $this->createForm(TranslateType::class, $formObject, $formOptions);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $selfRoute = $routePrefix . 'translate';
            if ($isPageStep) {
                $pageSlug = $page->getSlug();

                // collect translated fields for revisioning
                $translationData = [];

                // main language
                $language = $translatableHelper->getCurrentLanguage();
                $translationData[$language] = [];
                $translatableFields = $translatableHelper->getTranslatableFields('page');
                foreach ($translatableFields as $fieldName) {
                    $fieldData = isset($form[$fieldName]) ? $form[$fieldName]->getData() : '';
                    $translationData[$language][$fieldName] = $fieldData;
                }

                // other languages
                foreach ($supportedLanguages as $language) {
                    $translationInput = $translatableHelper->readTranslationInput($form, $language);
                    if (!count($translationInput)) {
                        continue;
                    }
                    $translationData[$language] = $translationInput;
                }

                $page->setTranslationData($translationData);
            }
            $page->set_actionDescriptionForLogEntry('_HISTORY_PAGE_TRANSLATION_UPDATED');
            // handle form data
            if (in_array($form->getClickedButton()->getName(), ['prev', 'next', 'saveandquit'])) {
                // update translations
                $success = $workflowHelper->executeAction($formObject, 'update');
                $translatableHelper->processEntityAfterEditing($formObject, $form);
            }
            if (!$isPageStep) {
                // create new log entry
                $loggableHelper->updateContentData($page);
                $success = $workflowHelper->executeAction($page, 'update');
            }

            if (!$isPageStep && $form->get('prev')->isClicked()) {
                if (null !== $translationInfo['previousContentId']) {
                    $returnUrl = $this->generateUrl($selfRoute, ['slug' => $pageSlug, 'cid' => $translationInfo['previousContentId']]);
                } else {
                    $returnUrl = $this->generateUrl($selfRoute, ['slug' => $pageSlug]);
                }
            }
            if (null !== $translationInfo['nextContentId'] && in_array($form->getClickedButton()->getName(), ['next', 'skip'])) {
                $returnUrl = $this->generateUrl($selfRoute, ['slug' => $pageSlug, 'cid' => $translationInfo['nextContentId']]);
            }

            if (true === $hasPageLockModule) {
                $lockingApi->releaseLock($lockName);
            }

            return $this->redirect($returnUrl);
        }

        $mandatoryFieldsPerLocale = $translatableHelper->getMandatoryFields('page');
        $localesWithMandatoryFields = [];
        foreach ($mandatoryFieldsPerLocale as $locale => $fields) {
            if (count($fields) > 0) {
                $localesWithMandatoryFields[] = $locale;
            }
        }
        if (!in_array($translatableHelper->getCurrentLanguage(), $localesWithMandatoryFields)) {
            $localesWithMandatoryFields[] = $translatableHelper->getCurrentLanguage();
        }

        $localesWithExistingData = [$request->getLocale()];
        foreach ($translations as $language => $translationData) {
            foreach ($translationData as $fieldName => $fieldContent) {
                if (empty($fieldContent)) {
                    continue;
                }
                $localesWithExistingData[] = $language;
                break;
            }
        }

        return [
            'routeArea' => $routeArea,
            'currentStep' => $currentStep,
            'amountOfSteps' => (count($page->getContentItems()) + 1),
            'translationInfo' => $translationInfo,
            'supportedLanguages' => $supportedLanguages,
            'localesWithExistingData' => $localesWithExistingData,
            'localesWithMandatoryFields' => $localesWithMandatoryFields,
            'form' => $form->createView(),
            'page' => $page,
            'pageSlug' => $pageSlug,
            'contentItem' => $contentItem,
            'contentType' => $contentType
        ];
    }

    /**
     * Displays sub pages of a given page.
     *
     * @Route("/subpages/{slug}.{_format}",
     *        requirements = {"slug" = "[^.]+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     *
     * @param Request $request
     * @param PermissionHelper $permissionHelper
     * @param EntityFactory $entityFactory
     * @param ViewHelper $viewHelper
     * @param string $slug Slug of treated page instance
     *
     * @return Response Output
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be displayed isn't found
     */
    public function subpagesAction(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        ViewHelper $viewHelper,
        $slug
    ) {
        $page = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->__('No such page found.'));
        }

        // permission check
        if (!$permissionHelper->hasEntityPermission($page, ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        return $viewHelper->processTemplate('page', 'subpages', [
            'page' => $page,
            'routeArea' => ''
        ]);
    }
    
    /**
     * @inheritDoc
     *
     * @Theme("admin")
     */
    public function adminDisplayAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        CategoryHelper $categoryHelper,
        FeatureActivationHelper $featureActivationHelper,
        LoggableHelper $loggableHelper,
        $slug
    ) {
        return $this->displayInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $entityFactory, $categoryHelper, $featureActivationHelper, $loggableHelper, $slug, true);
    }
    
    /**
     * @inheritDoc
     */
    public function displayAction(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        CategoryHelper $categoryHelper,
        FeatureActivationHelper $featureActivationHelper,
        LoggableHelper $loggableHelper,
        $slug
    ) {
        return $this->displayInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $entityFactory, $categoryHelper, $featureActivationHelper, $loggableHelper, $slug, false);
    }
    
    /**
     * @inheritDoc
     * @Route("/admin/pages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     * @Theme("admin")
     */
    public function adminHandleSelectedEntriesAction(
        Request $request,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        CurrentUserApiInterface $currentUserApi
    ) {
        return $this->handleSelectedEntriesActionInternal($request, $entityFactory, $workflowHelper, $hookHelper, $currentUserApi, true);
    }
    
    /**
     * @inheritDoc
     * @Route("/pages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     */
    public function handleSelectedEntriesAction(
        Request $request,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        CurrentUserApiInterface $currentUserApi
    ) {
        return $this->handleSelectedEntriesActionInternal($request, $entityFactory, $workflowHelper, $hookHelper, $currentUserApi, false);
    }
}
