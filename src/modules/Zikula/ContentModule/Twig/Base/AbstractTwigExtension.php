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

namespace Zikula\ContentModule\Twig\Base;

use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Renderer\ListRenderer;
use Symfony\Component\Routing\RouterInterface;
use Twig_Extension;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\ListEntriesHelper;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\WorkflowHelper;
use Zikula\ContentModule\Menu\MenuBuilder;

/**
 * Twig extension base class.
 */
abstract class AbstractTwigExtension extends Twig_Extension
{
    use TranslatorTrait;
    
    /**
     * @var RouterInterface
     */
    protected $router;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var WorkflowHelper
     */
    protected $workflowHelper;
    
    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;
    
    /**
     * @var MenuBuilder
     */
    protected $menuBuilder;
    
    /**
     * TwigExtension constructor.
     *
     * @param TranslatorInterface $translator     Translator service instance
     * @param Routerinterface     $router         Router service instance
     * @param VariableApiInterface   $variableApi    VariableApi service instance
     * @param EntityFactory       $entityFactory     EntityFactory service instance
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     * @param WorkflowHelper      $workflowHelper WorkflowHelper service instance
     * @param ListEntriesHelper   $listHelper     ListEntriesHelper service instance
     * @param MenuBuilder         $menuBuilder    MenuBuilder service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        VariableApiInterface $variableApi,
        EntityFactory $entityFactory,
        EntityDisplayHelper $entityDisplayHelper,
        WorkflowHelper $workflowHelper,
        ListEntriesHelper $listHelper,
        MenuBuilder $menuBuilder)
    {
        $this->setTranslator($translator);
        $this->router = $router;
        $this->variableApi = $variableApi;
        $this->entityFactory = $entityFactory;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->workflowHelper = $workflowHelper;
        $this->listHelper = $listHelper;
        $this->menuBuilder = $menuBuilder;
    }
    
    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * Returns a list of custom Twig functions.
     *
     * @return \Twig_SimpleFunction[] List of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('zikulacontentmodule_treeData', [$this, 'getTreeData'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('zikulacontentmodule_treeSelection', [$this, 'getTreeSelection']),
            new \Twig_SimpleFunction('zikulacontentmodule_objectTypeSelector', [$this, 'getObjectTypeSelector']),
            new \Twig_SimpleFunction('zikulacontentmodule_templateSelector', [$this, 'getTemplateSelector'])
        ];
    }
    
    /**
     * Returns a list of custom Twig filters.
     *
     * @return \Twig_SimpleFilter[] List of filters
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('zikulacontentmodule_listEntry', [$this, 'getListEntry']),
            new \Twig_SimpleFilter('zikulacontentmodule_formattedTitle', [$this, 'getFormattedEntityTitle']),
            new \Twig_SimpleFilter('zikulacontentmodule_objectState', [$this, 'getObjectState'], ['is_safe' => ['html']])
        ];
    }
    
    /**
     * Returns a list of custom Twig tests.
     *
     * @return \Twig_SimpleTest[] List of tests
     */
    public function getTests()
    {
        return [
            new \Twig_SimpleTest('zikulacontentmodule_instanceOf', function ($var, $instance) {
                return $var instanceof $instance;
            })
        ];
    }
    
    /**
     * The zikulacontentmodule_objectState filter displays the name of a given object's workflow state.
     * Examples:
     *    {{ item.workflowState|zikulacontentmodule_objectState }}        {# with visual feedback #}
     *    {{ item.workflowState|zikulacontentmodule_objectState(false) }} {# no ui feedback #}
     *
     * @param string  $state      Name of given workflow state
     * @param boolean $uiFeedback Whether the output should include some visual feedback about the state
     *
     * @return string Enriched and translated workflow state ready for display
     */
    public function getObjectState($state = 'initial', $uiFeedback = true)
    {
        $stateInfo = $this->workflowHelper->getStateInfo($state);
    
        $result = $stateInfo['text'];
        if (true === $uiFeedback) {
            $result = '<span class="label label-' . $stateInfo['ui'] . '">' . $result . '</span>';
        }
    
        return $result;
    }
    
    
    /**
     * The zikulacontentmodule_listEntry filter displays the name
     * or names for a given list item.
     * Example:
     *     {{ entity.listField|zikulacontentmodule_listEntry('entityName', 'fieldName') }}
     *
     * @param string $value      The dropdown value to process
     * @param string $objectType The treated object type
     * @param string $fieldName  The list field's name
     * @param string $delimiter  String used as separator for multiple selections
     *
     * @return string List item name
     */
    public function getListEntry($value, $objectType = '', $fieldName = '', $delimiter = ', ')
    {
        if ((empty($value) && $value != '0') || empty($objectType) || empty($fieldName)) {
            return $value;
        }
    
        return $this->listHelper->resolve($value, $objectType, $fieldName, $delimiter);
    }
    
    
    /**
     * The zikulacontentmodule_treeData function delivers the html output for a JS tree
     * based on given tree entities.
     *
     * @param string  $objectType Name of treated object type
     * @param array   $tree       Object collection with tree items
     * @param string  $routeArea  Either 'admin' or an emptyy string
     * @param integer $rootId     Optional id of root node, defaults to 1
     *
     * @return string Output markup
     */
    public function getTreeData($objectType, $tree = [], $routeArea = '', $rootId = 1)
    {
        // check whether an edit action is available
        $hasEditAction = in_array($objectType, ['page']);
    
        $repository = $this->entityFactory->getRepository($objectType);
        $descriptionFieldName = $this->entityDisplayHelper->getDescriptionFieldName($objectType);
    
        $result = [
            'nodes' => '',
            'actions' => ''
        ];
        foreach ($tree as $node) {
            if ($node->getLvl() < 1 || $node->getKey() == $rootId) {
                list ($nodes, $actions) = $this->processTreeItemWithChildren($objectType, $node, $routeArea, $rootId, $descriptionFieldName, $hasEditAction);
                $result['nodes'] .= $nodes;
                $result['actions'] .= $actions;
            }
        }
    
        return $result;
    }
    
    /**
     * Builds an unordered list for a tree node and it's children.
     *
     * @param string  $objectType           Name of treated object type
     * @param object  $node                 The processed tree node
     * @param string  $routeArea            Either 'admin' or an emptyy string
     * @param integer $rootId               Optional id of root node, defaults to 1
     * @param string  $descriptionFieldName Name of field to be used as a description
     * @param boolean $hasEditAction        Whether item editing is possible or not
     *
     * @return string Output markup
     */
    protected function processTreeItemWithChildren($objectType, $node, $routeArea, $rootId, $descriptionFieldName, $hasEditAction)
    {
        $idPrefix = 'tree' . $rootId . 'node_' . $node->getKey();
        $title = $descriptionFieldName != '' ? strip_tags($node[$descriptionFieldName]) : '';
    
        $needsArg = in_array($objectType, ['page']);
        $urlArgs = $needsArg ? $node->createUrlArgs(true) : $node->createUrlArgs();
        $urlDataAttributes = '';
        foreach ($urlArgs as $field => $value) {
            $urlDataAttributes .= ' data-' . $field . '="' . $value . '"';
        }
    
        $liTag = '<li id="' . $idPrefix . '" title="' . str_replace('"', '', $title) . '" class="lvl' . $node->getLvl() . '"' . $urlDataAttributes . '>';
        $liContent = $this->entityDisplayHelper->getFormattedTitle($node);
        if ($hasEditAction) {
            $url = $this->router->generate('zikulacontentmodule_' . strtolower($objectType) . '_' . $routeArea . 'edit', $urlArgs);
            $liContent = '<a href="' . $url . '" title="' . str_replace('"', '', $title) . '">' . $liContent . '</a>';
        }
    
        $nodeItem = $liTag . $liContent;
    
        $itemActionsMenu = $this->menuBuilder->createItemActionsMenu(['entity' => $node, 'area' => $routeArea, 'context' => 'view']);
        $renderer = new ListRenderer(new Matcher());
    
        $actions = '<li id="itemActions' . $node->getKey() . '">';
        $actions .= $renderer->render($itemActionsMenu);
        $actions = str_replace(' class="first"', '', $actions);
        $actions = str_replace(' class="last"', '', $actions);
        $actions .= '</li>';
    
        if (count($node->getChildren()) > 0) {
            $nodeItem .= '<ul>';
            foreach ($node->getChildren() as $childNode) {
                list ($subNodes, $subActions) = $this->processTreeItemWithChildren($objectType, $childNode, $routeArea, $rootId, $descriptionFieldName, $hasEditAction);
                $nodeItem .= $subNodes;
                $actions .= $subActions;
            }
            $nodeItem .= '</ul>';
        }
    
        $nodeItem .= '</li>';
    
        return [$nodeItem, $actions];
    }
    
    
    /**
     * The zikulacontentmodule_treeSelection function retrieves tree entities based on a given one.
     *
     * Available parameters:
     *   - objectType:   Name of treated object type.
     *   - node:         Given entity as tree entry point.
     *   - target:       One of 'allParents', 'directParent', 'allChildren', 'directChildren', 'predecessors', 'successors', 'preandsuccessors'
     *   - skipRootNode: Whether root nodes are skipped or not (defaults to true). Useful for when working with many trees at once.
     *
     * @return string The output of the plugin
     */
    public function getTreeSelection($objectType, $node, $target, $skipRootNode = true)
    {
        $repository = $this->entityFactory->getRepository($objectType);
        $titleFieldName = $this->entityDisplayHelper->getTitleFieldName($objectType);
    
        $result = null;
    
        switch ($target) {
            case 'allParents':
            case 'directParent':
                $path = $repository->getPath($node);
                if (count($path) > 0) {
                    // remove $node
                    unset($path[count($path)-1]);
                }
                if ($skipRootNode && count($path) > 0) {
                    // remove root level
                    array_shift($path);
                }
                if ($target == 'allParents') {
                    $result = $path;
                } elseif ($target == 'directParent' && count($path) > 0) {
                    $result = $path[count($path)-1];
                }
                break;
            case 'allChildren':
            case 'directChildren':
                $direct = $target == 'directChildren';
                $sortByField = $titleFieldName != '' ? $titleFieldName : null;
                $sortDirection = 'ASC';
                $result = $repository->children($node, $direct, $sortByField, $sortDirection);
                break;
            case 'predecessors':
                $includeSelf = false;
                $result = $repository->getPrevSiblings($node, $includeSelf);
                break;
            case 'successors':
                $includeSelf = false;
                $result = $repository->getNextSiblings($node, $includeSelf);
                break;
            case 'preandsuccessors':
                $includeSelf = false;
                $result = array_merge($repository->getPrevSiblings($node, $includeSelf), $repository->getNextSiblings($node, $includeSelf));
                break;
        }
    
        return $result;
    }
    
    
    /**
     * The zikulacontentmodule_objectTypeSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin
     */
    public function getObjectTypeSelector()
    {
        $result = [];
    
        $result[] = [
            'text' => $this->__('Pages'),
            'value' => 'page'
        ];
        $result[] = [
            'text' => $this->__('Content items'),
            'value' => 'contentItem'
        ];
        $result[] = [
            'text' => $this->__('Searchables'),
            'value' => 'searchable'
        ];
    
        return $result;
    }
    
    
    /**
     * The zikulacontentmodule_templateSelector function provides items for a dropdown selector.
     *
     * @return string The output of the plugin
     */
    public function getTemplateSelector()
    {
        $result = [];
    
        $result[] = [
            'text' => $this->__('Only item titles'),
            'value' => 'itemlist_display.html.twig'
        ];
        $result[] = [
            'text' => $this->__('With description'),
            'value' => 'itemlist_display_description.html.twig'
        ];
        $result[] = [
            'text' => $this->__('Custom template'),
            'value' => 'custom'
        ];
    
        return $result;
    }
    
    /**
     * The zikulacontentmodule_formattedTitle filter outputs a formatted title for a given entity.
     * Example:
     *     {{ myPost|zikulacontentmodule_formattedTitle }}
     *
     * @param object $entity The given entity instance
     *
     * @return string The formatted title
     */
    public function getFormattedEntityTitle($entity)
    {
        return $this->entityDisplayHelper->getFormattedTitle($entity);
    }
}
