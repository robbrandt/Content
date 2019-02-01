<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Helper\Base;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Helper\CategoryHelper;
use Zikula\ContentModule\Helper\PermissionHelper;

/**
 * Entity collection filter helper base class.
 */
abstract class AbstractCollectionFilterHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var bool Fallback value to determine whether only own entries should be selected or not
     */
    protected $showOnlyOwnEntries = false;
    
    /**
     * CollectionFilterHelper constructor.
     *
     * @param RequestStack $requestStack RequestStack service instance
     * @param PermissionHelper $permissionHelper PermissionHelper service instance
     * @param CurrentUserApiInterface $currentUserApi CurrentUserApi service instance
     * @param CategoryHelper $categoryHelper CategoryHelper service instance
     * @param VariableApiInterface $variableApi VariableApi service instance
     * @param boolean $showOnlyOwnEntries Fallback value to determine whether only own entries should be selected or not
     */
    public function __construct(
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        CurrentUserApiInterface $currentUserApi,
        CategoryHelper $categoryHelper,
        VariableApiInterface $variableApi,
        $showOnlyOwnEntries
    ) {
        $this->requestStack = $requestStack;
        $this->permissionHelper = $permissionHelper;
        $this->currentUserApi = $currentUserApi;
        $this->categoryHelper = $categoryHelper;
        $this->variableApi = $variableApi;
        $this->showOnlyOwnEntries = $showOnlyOwnEntries;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $objectType Name of treated entity type
     * @param string $context    Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args       Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    public function getViewQuickNavParameters($objectType = '', $context = '', array $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType'])) {
            $context = 'controllerAction';
        }
    
        if ($objectType == 'page') {
            return $this->getViewQuickNavParametersForPage($context, $args);
        }
        if ($objectType == 'contentItem') {
            return $this->getViewQuickNavParametersForContentItem($context, $args);
        }
    
        return [];
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param string       $objectType Name of treated entity type
     * @param QueryBuilder $qb         Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCommonViewFilters($objectType, QueryBuilder $qb)
    {
        if ($objectType == 'page') {
            return $this->addCommonViewFiltersForPage($qb);
        }
        if ($objectType == 'contentItem') {
            return $this->addCommonViewFiltersForContentItem($qb);
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param string       $objectType Name of treated entity type
     * @param QueryBuilder $qb         Query builder to be enhanced
     * @param array        $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function applyDefaultFilters($objectType, QueryBuilder $qb, array $parameters = [])
    {
        if ($objectType == 'page') {
            return $this->applyDefaultFiltersForPage($qb, $parameters);
        }
        if ($objectType == 'contentItem') {
            return $this->applyDefaultFiltersForContentItem($qb, $parameters);
        }
    
        return $qb;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args    Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForPage($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['catId'] = $request->query->get('catId', '');
        $parameters['catIdList'] = $this->categoryHelper->retrieveCategoriesFromRequest('page', 'GET');
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['q'] = $request->query->get('q', '');
        $parameters['showTitle'] = $request->query->get('showTitle', '');
        $parameters['skipHookSubscribers'] = $request->query->get('skipHookSubscribers', '');
        $parameters['active'] = $request->query->get('active', '');
        $parameters['inMenu'] = $request->query->get('inMenu', '');
    
        return $parameters;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array  $args    Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForContentItem($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['page'] = $request->query->get('page', 0);
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['scope'] = $request->query->get('scope', '');
        $parameters['q'] = $request->query->get('q', '');
        $parameters['active'] = $request->query->get('active', '');
    
        return $parameters;
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForPage(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForPage();
        foreach ($parameters as $k => $v) {
            if ($k == 'catId') {
                if (intval($v) > 0) {
                    // single category filter
                    $qb->andWhere('tblCategories.category = :category')
                       ->setParameter('category', $v);
                }
                continue;
            }
            if ($k == 'catIdList') {
                // multi category filter
                $qb = $this->categoryHelper->buildFilterClauses($qb, 'page', $v);
                continue;
            }
            if (in_array($k, ['q', 'searchterm'])) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('page', $qb, $v);
                }
                continue;
            }
            if (in_array($k, ['showTitle', 'skipHookSubscribers', 'active', 'inMenu'])) {
                // boolean filter
                if ($v == 'no') {
                    $qb->andWhere('tbl.' . $k . ' = 0');
                } elseif ($v == 'yes' || $v == '1') {
                    $qb->andWhere('tbl.' . $k . ' = 1');
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && $v != '') || (is_numeric($v) && $v > 0)) {
                if ($k == 'workflowState' && substr($v, 0, 1) == '!') {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1, strlen($v)-1));
                } elseif (substr($v, 0, 1) == '%') {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        $qb = $this->applyDefaultFiltersForPage($qb, $parameters);
    
        return $qb;
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForContentItem(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForContentItem();
        foreach ($parameters as $k => $v) {
            if (in_array($k, ['q', 'searchterm'])) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('contentItem', $qb, $v);
                }
                continue;
            }
            if (in_array($k, ['active'])) {
                // boolean filter
                if ($v == 'no') {
                    $qb->andWhere('tbl.' . $k . ' = 0');
                } elseif ($v == 'yes' || $v == '1') {
                    $qb->andWhere('tbl.' . $k . ' = 1');
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && $v != '') || (is_numeric($v) && $v > 0)) {
                if ($k == 'workflowState' && substr($v, 0, 1) == '!') {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1, strlen($v)-1));
                } elseif (substr($v, 0, 1) == '%') {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        $qb = $this->applyDefaultFiltersForContentItem($qb, $parameters);
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb         Query builder to be enhanced
     * @param array        $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForPage(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        $isAdminArea = false !== strpos($routeName, 'zikulacontentmodule_page_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$this->variableApi->get('ZikulaContentModule', 'pagePrivateMode', false);
    
        if (!in_array('workflowState', array_keys($parameters)) || empty($parameters['workflowState'])) {
            // per default we show approved pages only
            $onlineStates = ['approved'];
            if ($showOnlyOwnEntries) {
                // allow the owner to see his pages
                $onlineStates[] = 'deferred';
                $onlineStates[] = 'trashed';
            }
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        $qb = $this->applyDateRangeFilterForPage($qb);
        if (in_array('tblContentItems', $qb->getAllAliases())) {
            $qb = $this->applyDateRangeFilterForContentItem($qb, 'tblContentItems');
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb         Query builder to be enhanced
     * @param array        $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForContentItem(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route');
        $isAdminArea = false !== strpos($routeName, 'zikulacontentmodule_contentitem_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
    
        if (!in_array('workflowState', array_keys($parameters)) || empty($parameters['workflowState'])) {
            // per default we show approved content items only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        $qb = $this->applyDateRangeFilterForContentItem($qb);
        if (in_array('tblPage', $qb->getAllAliases())) {
            $qb = $this->applyDateRangeFilterForPage($qb, 'tblPage');
        }
    
        return $qb;
    }
    
    /**
     * Applies start and end date filters for selecting pages.
     *
     * @param QueryBuilder $qb    Query builder to be enhanced
     * @param string       $alias Table alias
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDateRangeFilterForPage(QueryBuilder $qb, $alias = 'tbl')
    {
        $request = $this->requestStack->getCurrentRequest();
        $startDate = $request->query->get('activeFrom', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.activeFrom <= :startDate OR ' . $alias . '.activeFrom IS NULL)')
           ->setParameter('startDate', $startDate);
    
        $endDate = $request->query->get('activeTo', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.activeTo >= :endDate OR ' . $alias . '.activeTo IS NULL)')
           ->setParameter('endDate', $endDate);
    
        return $qb;
    }
    
    /**
     * Applies start and end date filters for selecting content items.
     *
     * @param QueryBuilder $qb    Query builder to be enhanced
     * @param string       $alias Table alias
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDateRangeFilterForContentItem(QueryBuilder $qb, $alias = 'tbl')
    {
        $request = $this->requestStack->getCurrentRequest();
        $startDate = $request->query->get('activeFrom', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.activeFrom <= :startDate OR ' . $alias . '.activeFrom IS NULL)')
           ->setParameter('startDate', $startDate);
    
        $endDate = $request->query->get('activeTo', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.activeTo >= :endDate OR ' . $alias . '.activeTo IS NULL)')
           ->setParameter('endDate', $endDate);
    
        return $qb;
    }
    
    /**
     * Adds a where clause for search query.
     *
     * @param string       $objectType Name of treated entity type
     * @param QueryBuilder $qb         Query builder to be enhanced
     * @param string       $fragment   The fragment to search for
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addSearchFilter($objectType, QueryBuilder $qb, $fragment = '')
    {
        if ($fragment == '') {
            return $qb;
        }
    
        $filters = [];
        $parameters = [];
    
        if ($objectType == 'page') {
            $filters[] = 'tbl.workflowState = :searchWorkflowState';
            $parameters['searchWorkflowState'] = $fragment;
            $filters[] = 'tbl.title LIKE :searchTitle';
            $parameters['searchTitle'] = '%' . $fragment . '%';
            $filters[] = 'tbl.metaDescription LIKE :searchMetaDescription';
            $parameters['searchMetaDescription'] = '%' . $fragment . '%';
            $filters[] = 'tbl.views = :searchViews';
            $parameters['searchViews'] = $fragment;
            $filters[] = 'tbl.activeFrom = :searchActiveFrom';
            $parameters['searchActiveFrom'] = $fragment;
            $filters[] = 'tbl.activeTo = :searchActiveTo';
            $parameters['searchActiveTo'] = $fragment;
            $filters[] = 'tbl.optionalString1 LIKE :searchOptionalString1';
            $parameters['searchOptionalString1'] = '%' . $fragment . '%';
            $filters[] = 'tbl.optionalString2 LIKE :searchOptionalString2';
            $parameters['searchOptionalString2'] = '%' . $fragment . '%';
            $filters[] = 'tbl.optionalText LIKE :searchOptionalText';
            $parameters['searchOptionalText'] = '%' . $fragment . '%';
            $filters[] = 'tbl.currentVersion = :searchCurrentVersion';
            $parameters['searchCurrentVersion'] = $fragment;
        }
        if ($objectType == 'contentItem') {
            $filters[] = 'tbl.owningType LIKE :searchOwningType';
            $parameters['searchOwningType'] = '%' . $fragment . '%';
            $filters[] = 'tbl.activeFrom = :searchActiveFrom';
            $parameters['searchActiveFrom'] = $fragment;
            $filters[] = 'tbl.activeTo = :searchActiveTo';
            $parameters['searchActiveTo'] = $fragment;
            $filters[] = 'tbl.scope = :searchScope';
            $parameters['searchScope'] = $fragment;
            $filters[] = 'tbl.searchText LIKE :searchSearchText';
            $parameters['searchSearchText'] = '%' . $fragment . '%';
            $filters[] = 'tbl.additionalSearchText LIKE :searchAdditionalSearchText';
            $parameters['searchAdditionalSearchText'] = '%' . $fragment . '%';
        }
    
        $qb->andWhere('(' . implode(' OR ', $filters) . ')');
    
        foreach ($parameters as $parameterName => $parameterValue) {
            $qb->setParameter($parameterName, $parameterValue);
        }
    
        return $qb;
    }
    
    /**
     * Adds a filter for the createdBy field.
     *
     * @param QueryBuilder $qb     Query builder to be enhanced
     * @param integer      $userId The user identifier used for filtering
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCreatorFilter(QueryBuilder $qb, $userId = null)
    {
        if (null === $userId) {
            $userId = $this->currentUserApi->isLoggedIn() ? $this->currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        }
    
        if (is_array($userId)) {
            $qb->andWhere('tbl.createdBy IN (:userIds)')
               ->setParameter('userIds', $userId);
        } else {
            $qb->andWhere('tbl.createdBy = :userId')
               ->setParameter('userId', $userId);
        }
    
        return $qb;
    }
}
