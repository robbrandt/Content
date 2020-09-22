<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Controller;

use Zikula\ContentModule\Controller\Base\AbstractPageController;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Zikula\Bundle\CoreBundle\HttpKernel\ZikulaHttpKernelInterface;
use Zikula\Bundle\HookBundle\Category\UiHooksCategory;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;
use Zikula\ThemeModule\Engine\Annotation\Theme;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Form\Handler\Page\EditHandler;
use Zikula\ContentModule\Form\Type\TranslateType;
use Zikula\ContentModule\Helper\ControllerHelper;
use Zikula\ContentModule\Helper\ContentDisplayHelper;
use Zikula\ContentModule\Helper\HookHelper;
use Zikula\ContentModule\Helper\LoggableHelper;
use Zikula\ContentModule\Helper\LockHelper;
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
     * @Route("/admin/pages",
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminIndex(
        Request $request,
        PermissionHelper $permissionHelper
    ): Response {
        return $this->indexInternal($request, $permissionHelper, true);
    }
    
    /**
     * @Route("/pages",
     *        methods = {"GET"}
     * )
     */
    public function index(
        Request $request,
        PermissionHelper $permissionHelper
    ): Response {
        if (!$permissionHelper->hasComponentPermission('page', ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        return $this->redirectToRoute('zikulacontentmodule_page_sitemap');
    }
    
    /**
     * @Route("/admin/pages/view/{sort}/{sortdir}/{page}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "page" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "page" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminView(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        LoggableHelper $loggableHelper,
        string $sort,
        string $sortdir,
        int $page,
        int $num
    ): Response {
        return $this->viewInternal($request, $router, $permissionHelper, $controllerHelper, $viewHelper, $loggableHelper, $sort, $sortdir, $page, $num, true);
    }
    
    /**
     * @Route("/pages/view/{sort}/{sortdir}/{page}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "page" = "\d+", "num" = "\d+", "_format" = "html|csv|rss|atom|xml|json|pdf"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "page" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function view(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        LoggableHelper $loggableHelper,
        string $sort,
        string $sortdir,
        int $page,
        int $num
    ): Response {
        return $this->viewInternal($request, $router, $permissionHelper, $controllerHelper, $viewHelper, $loggableHelper, $sort, $sortdir, $page, $num, false);
    }
    
    /**
     * @Route("/admin/page/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"},
     *        options={"expose"=true}
     * )
     * @Theme("admin")
     */
    public function adminEdit(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EditHandler $formHandler
    ): Response {
        return $this->editInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $formHandler, true);
    }
    
    /**
     * @Route("/page/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"},
     *        options={"expose"=true}
     * )
     */
    public function edit(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EditHandler $formHandler
    ): Response {
        return $this->editInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $formHandler, false);
    }
    
    /**
     * @Route("/admin/page/deleted/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminUndelete(
        Request $request,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        int $id = 0
    ): Response {
        return $this->undeleteInternal($request, $loggableHelper, $translatableHelper, $id, true);
    }
    
    /**
     * @Route("/page/deleted/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function undelete(
        Request $request,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        int $id = 0
    ): Response {
        return $this->undeleteInternal($request, $loggableHelper, $translatableHelper, $id, false);
    }
    
    /**
     * @Route("/admin/page/history/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminLoggableHistory(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        string $slug = ''
    ): Response {
        return $this->loggableHistoryInternal($request, $permissionHelper, $entityFactory, $loggableHelper, $translatableHelper, $workflowHelper, $slug, true);
    }
    
    /**
     * @Route("/page/history/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     */
    public function loggableHistory(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        string $slug = ''
    ): Response {
        return $this->loggableHistoryInternal($request, $permissionHelper, $entityFactory, $loggableHelper, $translatableHelper, $workflowHelper, $slug, false);
    }
    
    /**
     * Handles management of content items for a given page.
     *
     * @Route("/admin/page/manageContent/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     * @Template("@ZikulaContentModule/Page/manageContent.html.twig")
     * @Theme("admin")
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    public function adminManageContent(
        Request $request,
        RouterInterface $router,
        ZikulaHttpKernelInterface $kernel,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LockHelper $lockHelper,
        string $slug = ''
    ): array {
        return $this->manageContentInternal($request, $router, $kernel, $permissionHelper, $entityFactory, $lockHelper, $slug, true);
    }

    /**
     * Handles management of content items for a given page.
     *
     * @Route("/page/manageContent/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     * @Template("@ZikulaContentModule/Page/manageContent.html.twig")
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    public function manageContent(
        Request $request,
        RouterInterface $router,
        ZikulaHttpKernelInterface $kernel,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LockHelper $lockHelper,
        string $slug = ''
    ): array {
        return $this->manageContentInternal($request, $router, $kernel, $permissionHelper, $entityFactory, $lockHelper, $slug, false);
    }

    /**
     * This method includes the common implementation code for adminManageContent() and manageContent().
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    protected function manageContentInternal(
        Request $request,
        RouterInterface $router,
        ZikulaHttpKernelInterface $kernel,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LockHelper $lockHelper,
        string $slug = '',
        bool $isAdmin = false
    ): array {
        /** @var PageEntity $page */
        $page = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->trans('No such page found.'));
        }

        if (!$permissionHelper->mayManagePageContent($page)) {
            throw new AccessDeniedException();
        }

        $routeArea = $isAdmin ? 'admin' : '';

        // detect return url
        $routePrefix = 'zikulacontentmodule_page_' . $routeArea;
        $returnUrl = $router->generate($routePrefix . 'display', $page->createUrlArgs());
        if ($request->headers->has('referer')) {
            $currentReferer = $request->headers->get('referer');
            if ($currentReferer !== $request->getUri()) {
                $returnUrl = $currentReferer;
            }
        }

        // try to guarantee that only one person at a time can be editing this entity
        $hasPageLockModule = $kernel->isBundle('ZikulaPageLockModule');
        $lockingApi = true === $hasPageLockModule ? $lockHelper->getLockingApi() : null;
        if (null !== $lockingApi) {
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
            'sectionStyles' => $sectionStyleChoices,
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
     * @throws NotFoundHttpException Thrown if the page was not found
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function updateLayout(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        int $id = 0
    ): JsonResponse {
        if (!$request->isXmlHttpRequest()) {
            return $this->json($this->trans('Only ajax access is allowed!'), Response::HTTP_BAD_REQUEST);
        }

        /** @var PageEntity $page */
        $page = $entityFactory->getRepository('page')->selectById($id);
        if (null === $page) {
            throw new NotFoundHttpException($this->trans('No such page found.'));
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
            return $this->json(['message' => $this->trans('Error! An error occured during layout persistence.')], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['message' => $this->trans('Done! Layout saved.')]);
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
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     */
    public function sitemap(
        Request $request,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory
    ): Response {
        if (!$permissionHelper->hasComponentPermission('page', ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        $ignoreFirstTreeLevel = $this->getVar('ignoreFirstTreeLevelInRoutes', true);
        $where = 'tbl.lvl = ' . ($ignoreFirstTreeLevel ? '1' : '0');
        $rootPages = $entityFactory->getRepository('page')->selectWhere($where, 'tbl.lft');

        return $this->render('@ZikulaContentModule/Page/sitemap.' . $request->getRequestFormat() . '.twig', [
            'pages' => $rootPages,
        ]);
    }
    
    /**
     * @Route("/admin/page/duplicate/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     */
    public function adminDuplicate(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        ModelHelper $modelHelper,
        HookHelper $hookHelper,
        string $slug = ''
    ): RedirectResponse {
        return $this->duplicateInternal($request, $router, $permissionHelper, $entityFactory, $workflowHelper, $modelHelper, $hookHelper, $slug, true);
    }

    /**
     * @Route("/page/duplicate/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET"}
     * )
     */
    public function duplicate(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        ModelHelper $modelHelper,
        HookHelper $hookHelper,
        string $slug = ''
    ): RedirectResponse {
        return $this->duplicateInternal($request, $router, $permissionHelper, $entityFactory, $workflowHelper, $modelHelper, $hookHelper, $slug, false);
    }

    /**
     * Handles duplication of a given page.
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be duplicated isn't found
     */
    protected function duplicateInternal(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        ModelHelper $modelHelper,
        HookHelper $hookHelper,
        string $slug = '',
        bool $isAdmin = false
    ): RedirectResponse {
        /** @var PageEntity $oldPage */
        $oldPage = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $oldPage) {
            throw new NotFoundHttpException($this->trans('No such page found.'));
        }

        if (!$permissionHelper->mayEdit($oldPage)) {
            throw new AccessDeniedException();
        }

        $routeArea = $isAdmin ? 'admin' : '';

        // detect return url
        $routePrefix = 'zikulacontentmodule_page_' . $routeArea;
        $returnUrl = $router->generate($routePrefix . 'view');
        if ($request->headers->has('referer')) {
            $currentReferer = $request->headers->get('referer');
            if ($currentReferer !== $request->getUri()) {
                $returnUrl = $currentReferer;
            }
        }

        $titleSuffix = ' - ' . $this->trans('new copy');
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

        $newPage->set_actionDescriptionForLogEntry('_HISTORY_PAGE_CLONED|%page%=' . $oldPage->getKey());

        $success = $workflowHelper->executeAction($newPage, 'submit');
        if (!$success) {
            $this->addFlash('error', $this->trans('Error! An error occured during duplicating the page.'));

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
                $this->addFlash('error', $this->trans('Error! An error occured during duplicating the page.'));

                continue;
            }

            $modelHelper->cloneContentTranslations($item->getId(), $newItem->getId());

            if ($newItem->supportsHookSubscribers()) {
                // Let any ui hooks know that we have updated the content item
                $hookHelper->callProcessHooks($newItem, UiHooksCategory::TYPE_PROCESS_EDIT);
            }

            if (is_array($layoutData) && 0 < count($layoutData)) {
                $oldItemId = $item->getId();
                $newItemId = $newItem->getId();
                foreach ($layoutData as $sectionKey => $section) {
                    if (!isset($section['widgets']) || !is_array($section['widgets']) || !count($section['widgets'])) {
                        continue;
                    }
                    foreach ($section['widgets'] as $widgetKey => $widget) {
                        if ($widget['id'] !== $oldItemId) {
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
            $this->addFlash('error', $this->trans('Error! An error occurred during duplicating the page.'));

            return $this->redirect($returnUrl);
        }

        $this->addFlash('success', $this->trans('Done! Page duplicated.'));

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
     *
     * @Template("@ZikulaContentModule/Page/translate.html.twig")
     *
     * @Theme("admin")
     *
     * @return array|RedirectResponse
     */
    public function adminTranslate(
        Request $request,
        RouterInterface $router,
        ZikulaHttpKernelInterface $kernel,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        ContentDisplayHelper $contentDisplayHelper,
        LockHelper $lockHelper,
        string $slug = ''
    ) {
        return $this->translateInternal($request, $router, $kernel, $permissionHelper, $entityFactory, $loggableHelper, $translatableHelper, $workflowHelper, $contentDisplayHelper, $lockHelper, $slug, true);
    }

    /**
     * @Route("/page/translate/{slug}",
     *        requirements = {"slug" = "[^.]+"},
     *        methods = {"GET", "POST"},
     *        options={"expose"=true}
     * )
     *
     * @Template("@ZikulaContentModule/Page/translate.html.twig")
     *
     * @return array|RedirectResponse
     */
    public function translate(
        Request $request,
        RouterInterface $router,
        ZikulaHttpKernelInterface $kernel,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        ContentDisplayHelper $contentDisplayHelper,
        LockHelper $lockHelper,
        string $slug = ''
    ) {
        return $this->translateInternal($request, $router, $kernel, $permissionHelper, $entityFactory, $loggableHelper, $translatableHelper, $workflowHelper, $contentDisplayHelper, $lockHelper, $slug, false);
    }

    /**
     * Handles page translation.
     *
     * @return array|RedirectResponse
     *
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be managed isn't found
     */
    protected function translateInternal(
        Request $request,
        RouterInterface $router,
        ZikulaHttpKernelInterface $kernel,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        TranslatableHelper $translatableHelper,
        WorkflowHelper $workflowHelper,
        ContentDisplayHelper $contentDisplayHelper,
        LockHelper $lockHelper,
        string $slug = '',
        $isAdmin = false
    ) {
        /** @var PageEntity $page */
        $page = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->trans('No such page found.'));
        }

        if (!$permissionHelper->mayEdit($page)) {
            throw new AccessDeniedException();
        }
        if (!$permissionHelper->mayManagePageContent($page)) {
            throw new AccessDeniedException();
        }

        $contentItemId = $request->query->getInt('cid');
        $contentItem = null;
        if ($contentItemId > 0) {
            foreach ($page->getContentItems() as $pageContentItem) {
                if ($contentItemId !== $pageContentItem->getId()) {
                    continue;
                }
                $contentItem = $pageContentItem;
                break;
            }
            if (null === $contentItem) {
                throw new NotFoundHttpException($this->trans('No such content found.'));
            }
        }

        $routeArea = $isAdmin ? 'admin' : '';

        // detect return url
        $routePrefix = 'zikulacontentmodule_page_' . $routeArea;
        $returnUrl = $router->generate($routePrefix . 'display', $page->createUrlArgs());

        // try to guarantee that only one person at a time can be editing this entity
        $hasPageLockModule = $kernel->isBundle('ZikulaPageLockModule');
        $lockingApi = true === $hasPageLockModule ? $lockHelper->getLockingApi() : null;
        $lockName = '';
        if (null !== $lockingApi) {
            $lockName = 'ZikulaContentModuleTranslatePage' . $page->getKey();

            $lockingApi->addLock($lockName, $returnUrl);
        }

        $supportedLanguages = $translatableHelper->getSupportedLanguages('page');

        $isPageStep = null === $contentItem;
        $currentStep = 1;
        $translationInfo = $translatableHelper->getTranslationInfo($page, $contentItem);

        $formObject = $isPageStep ? $page : $contentItem;
        $formOptions = [
            'mode' => $isPageStep ? 'page' : 'item',
            'translations' => [],
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
                if ($item->getEntity()->getId() !== $contentItemId) {
                    continue;
                }
                ++$currentStep;
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
            if (in_array($form->getClickedButton()->getName(), ['prev', 'next', 'saveandquit'], true)) {
                // update translations
                /*$success = */$workflowHelper->executeAction($formObject, 'update');
                $translatableHelper->processEntityAfterEditing($formObject, $form);
            }
            if (!$isPageStep) {
                // create new log entry
                $loggableHelper->updateContentData($page);
                /*$success = */$workflowHelper->executeAction($page, 'update');
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

            if (true === $hasPageLockModule && null !== $lockingApi) {
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
        if (!in_array($translatableHelper->getCurrentLanguage(), $localesWithMandatoryFields, true)) {
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
            'amountOfSteps' => count($page->getContentItems()) + 1,
            'translationInfo' => $translationInfo,
            'supportedLanguages' => $supportedLanguages,
            'localesWithExistingData' => $localesWithExistingData,
            'localesWithMandatoryFields' => $localesWithMandatoryFields,
            'form' => $form->createView(),
            'page' => $page,
            'pageSlug' => $pageSlug,
            'contentItem' => $contentItem,
            'contentType' => $contentType,
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
     * @throws AccessDeniedException Thrown if the user doesn't have required permissions
     * @throws NotFoundHttpException Thrown if page to be displayed isn't found
     */
    public function subpages(
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        ViewHelper $viewHelper,
        $slug
    ): Response {
        /** @var PageEntity $page */
        $page = $entityFactory->getRepository('page')->selectBySlug($slug);
        if (null === $page) {
            throw new NotFoundHttpException($this->trans('No such page found.'));
        }

        if (!$permissionHelper->hasEntityPermission($page, ACCESS_READ)) {
            throw new AccessDeniedException();
        }

        return $viewHelper->processTemplate('page', 'subpages', [
            'page' => $page,
            'routeArea' => '',
        ]);
    }
    
    /**
     * @Theme("admin")
     */
    public function adminDisplay(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        ?PageEntity $page = null,
        string $slug = ''
    ): Response {
        return $this->displayInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $entityFactory, $loggableHelper, $page, $slug, true);
    }
    
    public function display(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EntityFactory $entityFactory,
        LoggableHelper $loggableHelper,
        ?PageEntity $page = null,
        string $slug = ''
    ): Response {
        $page = null;
        if (is_numeric($slug)) {
            $page = $entityFactory->getRepository('page')->selectById($slug);
            if (null !== $page) {
                $slug = $page->getSlug();
            }
        }

        return $this->displayInternal($request, $permissionHelper, $controllerHelper, $viewHelper, $entityFactory, $loggableHelper, $page, $slug, false);
    }
    
    /**
     * @Route("/admin/pages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     * @Theme("admin")
     */
    public function adminHandleSelectedEntries(
        Request $request,
        LoggerInterface $logger,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        CurrentUserApiInterface $currentUserApi
    ): RedirectResponse {
        return $this->handleSelectedEntriesInternal($request, $logger, $entityFactory, $workflowHelper, $hookHelper, $currentUserApi, true);
    }
    
    /**
     * @Route("/pages/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     */
    public function handleSelectedEntries(
        Request $request,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        CurrentUserApiInterface $currentUserApi
    ): RedirectResponse {
        return $this->handleSelectedEntriesInternal($request, $entityFactory, $workflowHelper, $hookHelper, $currentUserApi, false);
    }
}
