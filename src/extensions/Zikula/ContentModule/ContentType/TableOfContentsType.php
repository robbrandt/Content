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

namespace Zikula\ContentModule\ContentType;

use Symfony\Component\Routing\RouterInterface;
use Zikula\ContentModule\ContentType\Form\Type\TableOfContentsType as FormType;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Helper\ContentDisplayHelper;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ExtensionsModule\ModuleInterface\Content\AbstractContentType;
use Zikula\ExtensionsModule\ModuleInterface\Content\ContentTypeInterface;

/**
 * Table of contents content type.
 */
class TableOfContentsType extends AbstractContentType
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var ContentDisplayHelper
     */
    protected $displayHelper;

    /**
     * @var boolean
     */
    protected $ignoreFirstTreeLevel;

    public function getCategory(): string
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    public function getIcon(): string
    {
        return 'book';
    }

    public function getTitle(): string
    {
        return $this->translator->trans('Table of contents', [], 'contentTypes');
    }

    public function getDescription(): string
    {
        return $this->translator->trans('A table of contents of headings and subpages (built from the available Content pages).', [], 'contentTypes');
    }

    public function getDefaultData(): array
    {
        $data = [
            'page' => 0,
            'includeSelf' => false,
            'includeStart' => false,
            'includeNotInMenu' => false,
            'includeHeading' => 0,
            'includeHeadingLevel' => 0,
            'includeSubpage' => 1,
            'includeSubpageLevel' => 0
        ];

        if (null !== $this->getEntity()) {
            $data['page'] = $this->getEntity()->getPage()->getId();
        }

        return $data;
    }

    public function displayView(): string
    {
        // get the current active page where this contentitem is in
        $this->data['currentPage'] = $this->getEntity()->getPage();

        $this->data['toc'] = [];
        $pageId = (int)($this->data['page'] ?? 0);
        if (!$pageId) {
            $pageId = 0;
        }

        $repository = $this->entityFactory->getRepository('page');
        $filters = [];

        if (0 === $pageId) {
            if (true === $this->ignoreFirstTreeLevel) {
                $filters[] = 'tbl.lvl = 1';
            } else {
                $filters[] = 'tbl.lvl = 0';
            }
        } else {
            $page = null;
            if ($pageId > 0) {
                /** @var PageEntity $page */
                $page = $repository->selectById($pageId);
                if (false === $page) {
                    return '';
                }
            }
            $filters[] = 'tbl.id = ' . $pageId;
        }
        if (!$this->data['includeNotInMenu']) {
            $filters[] = 'tbl.inMenu = 1';
        }

        $where = implode(' AND ', $filters);
        $useJoins = $this->data['includeHeading'] && $this->data['includeHeadingLevel'] > 0;

        $pages = $repository->selectWhere($where, 'tbl.lft', $useJoins);
        $this->data['toc']['toc'] = [];
        foreach ($pages as $page) {
            $this->data['toc']['toc'][] = $this->genTocRecursive($page, (0 === $pageId ? 1 : 0));
        }

        return parent::displayView();
    }

    protected function genTocRecursive(PageEntity $page, int $level): array
    {
        $toc = [];
        $pageUrl = $this->router->generate('zikulacontentmodule_page_display', ['slug' => $page->getSlug()]);

        $includeHeadings =
            1 === $this->data['includeHeading']
            || (
                2 === $this->data['includeHeading']
                && 0 <= $this->data['includeHeadingLevel'] - $level
            )
        ;
        if ($includeHeadings && count($page->getContentItems())) {
            foreach ($page->getContentItems() as $contentItem) {
                if ('Zikula\\ContentModule\\ContentType\\HeadingType' !== $contentItem->getOwningType()) {
                    continue;
                }
                $contentData = $contentItem->getContentData();
                if (isset($contentData['displayPageTitle']) && true === $contentData['displayPageTitle']) {
                    continue;
                }
                $contentType = $this->displayHelper->initContentType($contentItem);
                //$output = $contentType->displayView();
                $headingText = '';
                if ($contentType instanceof AbstractContentType) {
                    $headingData = $contentType->getData();
                    $headingText = $headingData['text'];
                }

                $toc[] = [
                    'title' => $headingText,
                    'url' => $pageUrl . '#heading_' . $contentItem->getId(),
                    'level' => $level,
                    'css' => 'content-toc-heading'
                ];
            }
        }

        $includeChildren =
            1 === $this->data['includeSubpage']
            || (
                2 === $this->data['includeSubpage']
                && 0 <= $this->data['includeSubpageLevel'] - $level
            )
        ;
        if ($includeChildren && count($page->getChildren())) {
            foreach ($page->getChildren() as $subPage) {
                $toc[] = $this->genTocRecursive($subPage, $level + 1);
            }
        }

        return [
            'pageId' => $page->getId(),
            'title' => $page->getTitle(),
            'url' => $pageUrl,
            'level' => $level,
            'css' => '',
            'toc' => $toc
        ];
    }

    public function getEditFormClass(): string
    {
        return FormType::class;
    }

    public function getAssets(string $context): array
    {
        $assets = parent::getAssets($context);
        if (ContentTypeInterface::CONTEXT_EDIT !== $context) {
            return $assets;
        }

        $assets['js'][] = $this->assetHelper->resolve(
            '@ZikulaContentModule:js/ZikulaContentModule.ContentType.TableOfContents.js'
        );

        return $assets;
    }

    public function getJsEntrypoint(string $context): ?string
    {
        if (ContentTypeInterface::CONTEXT_EDIT !== $context) {
            return null;
        }

        return 'contentInitTocEdit';
    }

    /**
     * @required
     */
    public function setAdditionalDepencies(
        RouterInterface $router,
        EntityFactory $entityFactory,
        ContentDisplayHelper $displayHelper,
        VariableApiInterface $variableApi
    ): void {
        $this->router = $router;
        $this->entityFactory = $entityFactory;
        $this->displayHelper = $displayHelper;
        $this->ignoreFirstTreeLevel = (bool)$variableApi->get(
            'ZikulaContentModule',
            'ignoreFirstTreeLevelInRoutes',
            true
        );
    }
}
