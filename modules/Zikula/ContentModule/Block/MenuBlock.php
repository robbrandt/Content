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
use Zikula\ContentModule\Block\Form\Type\MenuBlockType;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\ControllerHelper;

/**
 * Menu block implementation class.
 */
class MenuBlock extends AbstractBlockHandler
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
        return $this->trans('Content menu block');
    }
    
    public function display(array $properties = []): string
    {
        // only show block content if the user has the required permissions
        if (!$this->hasPermission('ZikulaContentModule:MenuBlock:', $properties['title'] . '::', ACCESS_OVERVIEW)) {
            return '';
        }

        // set default values for all params which are not properly set
        $defaults = $this->getDefaults();
        $properties = array_merge($defaults, $properties);

        $customFilters = [];
        if (0 < $properties['root']) {
            $customFilters[] = 'tbl.parent = ' . $properties['root'];
        /*} else {
            $customFilters[] = 'tbl.parent IS NULL';*/
        }
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
        $orderBy = 'tbl.lft';
        $repository = $this->entityFactory->getRepository('page');
        $qb = $repository->getListQueryBuilder($properties['filter'], $orderBy, false);

        // get objects from database
        $currentPage = 1;
        $resultsPerPage = $properties['amount'];
        $query = $repository->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
        try {
            list($entities, ) = $repository->retrieveCollectionResult($query, true);
        } catch (Exception $exception) {
            $entities = [];
        }

        // set a block title
        if (empty($properties['title'])) {
            $properties['title'] = $this->trans('Content menu');
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
        return '@ZikulaContentModule/Block/menu_display.html.twig';
    }

    public function getFormClassName(): string
    {
        return MenuBlockType::class;
    }

    public function getFormTemplate(): string
    {
        return '@ZikulaContentModule/Block/menu_modify.html.twig';
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
            'navType' => 0,
            'subPagesHandling' => 'hide',
            'root' => 0,
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
