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

namespace Zikula\ContentModule\Block;

use Exception;
use Zikula\BlocksModule\AbstractBlockHandler;
use Zikula\ContentModule\Block\Form\Type\SubPagesBlockType;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Helper\ControllerHelper;

/**
 * Sub pages block implementation class.
 */
class SubPagesBlock extends AbstractBlockHandler
{
    /**
     * @var ControllerHelper
     */
    private $controllerHelper;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    public function getType(): string
    {
        return $this->__('Sub pages of current page', 'zikulacontentmodule');
    }
    
    public function display(array $properties = []): string
    {
        // only show block content if the user has the required permissions
        if (!$this->hasPermission('ZikulaContentModule:SubPagesBlock:', $properties['title'] . '::', ACCESS_OVERVIEW)) {
            return '';
        }

        $entities = [];

        $pageId = 0;
        $repository = $this->entityFactory->getRepository('page');
        $request = $this->requestStack->getCurrentRequest();
        $routeName = $request->get('_route');
        if (in_array($routeName, ['zikulacontentmodule_page_display', 'zikulacontentmodule_page_admindisplay'])
            && $request->attributes->has('slug')) {
            /** @var PageEntity $page */
            $page = $repository->selectBySlug($request->attributes->get('slug'), false);
            $pageId = null !== $page ? $page->getId() : null;
        }
        if ($pageId > 0) {
            // set default values for all params which are not properly set
            $defaults = $this->getDefaults();
            $properties = array_merge($defaults, $properties);

            $customFilters = [];
            $customFilters[] = 'tbl.parent = ' . $pageId;
            if (true === $properties['inMenu']) {
                $customFilters[] = 'tbl.inMenu = 1';
            }

            if (count($customFilters) > 0) {
                if (!empty($properties['filter'])) {
                    $properties['filter'] = '(' . $properties['filter'] . ') AND ' . implode(' AND ', $customFilters);
                } else {
                    $properties['filter'] = implode(' AND ', $customFilters);
                }
            }

            // create query
            $orderBy = '';
            $qb = $repository->getListQueryBuilder($properties['filter'], $orderBy);

            // get objects from database
            $currentPage = 1;
            $resultsPerPage = $properties['amount'];
            $query = $repository->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
            try {
                list($entities, ) = $repository->retrieveCollectionResult($query, true);
            } catch (Exception $exception) {
                $entities = [];
            }
        }

        // set a block title
        if (empty($properties['title'])) {
            $properties['title'] = $this->__('Content subpages', 'zikulacontentmodule');
        }

        $template = $this->getDisplayTemplate($properties);

        $templateParameters = [
            'vars' => $properties,
            'items' => $entities
        ];

        $templateParameters = $this->controllerHelper->addTemplateParameters('page', $templateParameters, 'block');

        return $this->renderView($template, $templateParameters);
    }

    protected function getDisplayTemplate(array $properties = []): string
    {
        return '@ZikulaContentModule/Block/subpages_display.html.twig';
    }

    public function getFormClassName(): string
    {
        return SubPagesBlockType::class;
    }

    public function getFormTemplate(): string
    {
        return '@ZikulaContentModule/Block/subpages_modify.html.twig';
    }

    public function getFormOptions(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request && $request->attributes->has('blockEntity')) {
            $blockEntity = $request->attributes->get('blockEntity');
            if (is_object($blockEntity) && method_exists($blockEntity, 'getProperties')) {
                $blockProperties = $blockEntity->getProperties();
                if (!isset($blockProperties['amount'])) {
                    // set default options for new block creation
                    $blockEntity->setProperties($this->getDefaults());
                }
            }
        }
    
        return [];
    }

    /**
     * Returns default settings for this block.
     */
    protected function getDefaults(): array
    {
        return [
            'amount' => 5,
            'inMenu' => true,
            'filter' => ''
        ];
    }

    /**
     * @required
     */
    public function setControllerHelper(ControllerHelper $controllerHelper): void
    {
        $this->controllerHelper = $controllerHelper;
    }

    /**
     * @required
     */
    public function setEntityFactory(EntityFactory $entityFactory): void
    {
        $this->entityFactory = $entityFactory;
    }
}