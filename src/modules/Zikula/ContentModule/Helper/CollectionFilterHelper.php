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

namespace Zikula\ContentModule\Helper;

use Doctrine\ORM\QueryBuilder;
use Zikula\ContentModule\Helper\Base\AbstractCollectionFilterHelper;

/**
 * Entity collection filter helper implementation class.
 */
class CollectionFilterHelper extends AbstractCollectionFilterHelper
{
    protected function applyDefaultFiltersForPage(QueryBuilder $qb, array $parameters = []): QueryBuilder
    {
        $qb = parent::applyDefaultFiltersForPage($qb, $parameters);
        if (true === $this->skipDefaultFilters()) {
            return $qb;
        }

        $qb->andWhere('tbl.active = 1');
        if (true === $this->ignoreFirstTreeLevel()) {
            $qb->andWhere('tbl.lvl > 0');
        }
        if (in_array('tblContentItems', $qb->getAllAliases(), true)) {
            $request = $this->requestStack->getCurrentRequest();
            $routeName = null !== $request ? $request->get('_route', '') : '';
            if (!in_array($routeName, ['zikulacontentmodule_page_display', 'zikulacontentmodule_external_finder'])) {
                $qb->andWhere('tblContentItems.active = 1');
                $qb = $this->applyDateRangeFilterForContentItem($qb, 'tblContentItems');
            }
        }

        return $qb;
    }

    protected function applyDefaultFiltersForContentItem(QueryBuilder $qb, array $parameters = []): QueryBuilder
    {
        $qb = parent::applyDefaultFiltersForContentItem($qb, $parameters);
        if (true === $this->skipDefaultFilters()) {
            return $qb;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request || $request->getSession()->has('ContentAllowInactiveElements')) {
            return $qb;
        }

        $qb->andWhere('tbl.active = 1');
        if (in_array('tblPage', $qb->getAllAliases(), true)) {
            $qb->andWhere('tblPage.active = 1');
            $qb = $this->applyDateRangeFilterForPage($qb, 'tblPage');
        }

        return $qb;
    }

    /**
     * Checks if default filters should be skipped for the current request.
     */
    protected function skipDefaultFilters(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return true;
        }
        if ($request->isXmlHttpRequest()) {
            return true;
        }
        $routeName = $request->get('_route', '');
        $isAdminArea = false !== strpos($routeName, 'zikulacontentmodule_page_admin')
            || 'zikulacontentmodule_page_edit' === $routeName
            || false !== strpos($routeName, 'zikulacontentmodule_contentitem_admin')
        ;
        if ($isAdminArea/* || $this->permissionHelper->hasComponentPermission('page', ACCESS_ADD)*/) {
            return true;
        }
        if (1 === $request->query->getInt('preview') && $this->permissionHelper->hasComponentPermission('page', ACCESS_ADMIN)) {
            return true;
        }
        if (in_array($routeName, [
            'zikulacontentmodule_page_managecontent',
            'zikulacontentmodule_contentitem_displayediting',
            'zikulacontentmodule_page_sitemap'
        ], true)) {
            return true;
        }

        return false;
    }

    public function ignoreFirstTreeLevel(): bool
    {
        return (bool)$this->variableApi->get('ZikulaContentModule', 'ignoreFirstTreeLevelInRoutes', true);
    }
}
