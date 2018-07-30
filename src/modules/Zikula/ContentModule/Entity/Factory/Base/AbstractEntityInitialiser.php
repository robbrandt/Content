<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Entity\Factory\Base;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Entity\SearchableEntity;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ContentModule\Helper\ListEntriesHelper;

/**
 * Entity initialiser class used to dynamically apply default values to newly created entities.
 */
abstract class AbstractEntityInitialiser
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    /**
     * @var ListEntriesHelper Helper service for managing list entries
     */
    protected $listEntriesHelper;

    /**
     * EntityInitialiser constructor.
     *
     * @param RequestStack $requestStack RequestStack service instance
     * @param PermissionHelper $permissionHelper PermissionHelper service instance
     * @param ListEntriesHelper $listEntriesHelper Helper service for managing list entries
     */
    public function __construct(
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        ListEntriesHelper $listEntriesHelper
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->permissionHelper = $permissionHelper;
        $this->listEntriesHelper = $listEntriesHelper;
    }

    /**
     * Initialises a given page instance.
     *
     * @param PageEntity $entity The newly created entity instance
     *
     * @return PageEntity The updated entity instance
     */
    public function initPage(PageEntity $entity)
    {
        return $entity;
    }

    /**
     * Initialises a given contentItem instance.
     *
     * @param ContentItemEntity $entity The newly created entity instance
     *
     * @return ContentItemEntity The updated entity instance
     */
    public function initContentItem(ContentItemEntity $entity)
    {
        $listEntries = $this->listEntriesHelper->getEntries('contentItem', 'scope');
        foreach ($listEntries as $listEntry) {
            if (true === $listEntry['default']) {
                $entity->setScope($listEntry['value']);
                break;
            }
        }

        return $entity;
    }

    /**
     * Initialises a given searchable instance.
     *
     * @param SearchableEntity $entity The newly created entity instance
     *
     * @return SearchableEntity The updated entity instance
     */
    public function initSearchable(SearchableEntity $entity)
    {
        $entity->setSearchLanguage($this->request->getLocale());

        return $entity;
    }

    /**
     * Returns the list entries helper.
     *
     * @return ListEntriesHelper
     */
    public function getListEntriesHelper()
    {
        return $this->listEntriesHelper;
    }
    
    /**
     * Sets the list entries helper.
     *
     * @param ListEntriesHelper $listEntriesHelper
     *
     * @return void
     */
    public function setListEntriesHelper($listEntriesHelper)
    {
        if ($this->listEntriesHelper != $listEntriesHelper) {
            $this->listEntriesHelper = $listEntriesHelper;
        }
    }
    
}
