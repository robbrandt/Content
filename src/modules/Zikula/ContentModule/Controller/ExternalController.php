<?php

declare(strict_types=1);

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Zikula\ThemeModule\Engine\Asset;
use Zikula\ContentModule\Controller\Base\AbstractExternalController;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\CollectionFilterHelper;
use Zikula\ContentModule\Helper\ControllerHelper;
use Zikula\ContentModule\Helper\ListEntriesHelper;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ContentModule\Helper\ViewHelper;

/**
 * Controller for external calls implementation class.
 *
 * @Route("/external")
 */
class ExternalController extends AbstractExternalController
{
    /**
     * @inheritDoc
     * @Route("/display/{objectType}/{id}/{source}/{displayMode}",
     *        requirements = {"id" = "\d+", "source" = "block|contentType|scribite", "displayMode" = "link|embed"},
     *        defaults = {"source" = "contentType", "displayMode" = "embed"},
     *        methods = {"GET"}
     * )
     */
    public function displayAction(
        Request $request,
        ControllerHelper $controllerHelper,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        ViewHelper $viewHelper,
        string $objectType,
        int $id,
        string $source,
        string $displayMode
    ): Response
     {
        return parent::displayAction($request, $controllerHelper, $permissionHelper, $entityFactory, $viewHelper, $objectType, $id, $source, $displayMode);
    }

    /**
     * @inheritDoc
     * @Route("/finder/{objectType}/{editor}/{sort}/{sortdir}/{pos}/{num}",
     *        requirements = {"editor" = "ckeditor|quill|summernote|tinymce", "sortdir" = "asc|desc", "pos" = "\d+", "num" = "\d+"},
     *        defaults = {"sort" = "dummy", "sortdir" = "asc", "pos" = 1, "num" = 0},
     *        methods = {"GET"},
     *        options={"expose"=true}
     * )
     */
    public function finderAction(
        Request $request,
        ControllerHelper $controllerHelper,
        PermissionHelper $permissionHelper,
        EntityFactory $entityFactory,
        CollectionFilterHelper $collectionFilterHelper,
        ListEntriesHelper $listEntriesHelper,
        ViewHelper $viewHelper,
        Asset $assetHelper,
        string $objectType,
        string $editor,
        string $sort,
        string $sortdir,
        int $pos = 1,
        int $num = 0
    ): Response
     {
        return parent::finderAction($request, $controllerHelper, $permissionHelper, $entityFactory, $collectionFilterHelper, $listEntriesHelper, $viewHelper, $assetHelper, $objectType, $editor, $sort, $sortdir, $pos, $num);
    }

    // feel free to extend the external controller here
}
