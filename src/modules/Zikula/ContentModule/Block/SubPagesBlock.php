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

namespace Zikula\ContentModule\Block;

use Zikula\BlocksModule\AbstractBlockHandler;
use Zikula\ContentModule\Block\Form\Type\SubPagesBlockType;

/**
 * Sub pages block implementation class.
 */
class SubPagesBlock extends AbstractBlockHandler
{
    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->__('Sub pages of current page');
    }
    
    /**
     * @inheritDoc
     */
    public function display(array $properties = [])
    {
        // only show block content if the user has the required permissions
        if (!$this->hasPermission('ZikulaContentModule:SubPagesBlock:', "$properties[title]::", ACCESS_OVERVIEW)) {
            return '';
        }

        $entities = [];
        $objectCount = 0;

        $pageId = 0;
        $repository = $this->get('zikula_content_module.entity_factory')->getRepository('page');
        $request = $this->get('request_stack')->getCurrentRequest();
        $routeName = $request->get('_route');
        if (in_array($routeName, ['zikulacontentmodule_page_display', 'zikulacontentmodule_page_admindisplay'])
            && $request->attributes->has('slug')) {
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
                list($entities, $objectCount) = $repository->retrieveCollectionResult($query, true);
            } catch (\Exception $exception) {
                $entities = [];
                $objectCount = 0;
            }
        }

        // set a block title
        if (empty($properties['title'])) {
            $properties['title'] = $this->__('ZikulaContentModule subpages');
        }

        $template = $this->getDisplayTemplate($properties);

        $templateParameters = [
            'vars' => $properties,
            'items' => $entities
        ];

        $templateParameters = $this->get('zikula_content_module.controller_helper')->addTemplateParameters('page', $templateParameters, 'block', []);

        return $this->renderView($template, $templateParameters);
    }

    /**
     * Returns the template used for output.
     *
     * @param array $properties The block properties
     *
     * @return string the template path
     */
    protected function getDisplayTemplate(array $properties = [])
    {
        return '@ZikulaContentModule/Block/subpages_display.html.twig';
    }

    /**
     * @inheritDoc
     */
    public function getFormClassName()
    {
        return SubPagesBlockType::class;
    }

    /**
     * @inheritDoc
     */
    public function getFormTemplate()
    {
        return '@ZikulaContentModule/Block/subpages_modify.html.twig';
    }

    /**
     * @inheritDoc
     */
    public function getFormOptions()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        if ($request->attributes->has('blockEntity')) {
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
     *
     * @return array The default settings
     */
    protected function getDefaults()
    {
        return [
            'amount' => 5,
            'inMenu' => true,
            'filter' => ''
        ];
    }
}