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

namespace Zikula\ContentModule\Entity\Factory\Base;

use DateTime;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Helper\ListEntriesHelper;
use Zikula\ContentModule\Helper\PermissionHelper;

/**
 * Entity initialiser class used to dynamically apply default values to newly created entities.
 */
abstract class AbstractEntityInitialiser
{
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    /**
     * @var ListEntriesHelper
     */
    protected $listEntriesHelper;

    public function __construct(
        PermissionHelper $permissionHelper,
        ListEntriesHelper $listEntriesHelper
    ) {
        $this->permissionHelper = $permissionHelper;
        $this->listEntriesHelper = $listEntriesHelper;
    }

    /**
     * Initialises a given page instance.
     */
    public function initPage(PageEntity $entity): PageEntity
    {
        $listEntries = $this->listEntriesHelper->getEntries('page', 'scope');
        $items = [];
        foreach ($listEntries as $listEntry) {
            if (true === $listEntry['default']) {
                $items[] = $listEntry['value'];
            }
        }
        $entity->setScope(implode('###', $items));

        return $entity;
    }

    /**
     * Initialises a given contentItem instance.
     */
    public function initContentItem(ContentItemEntity $entity): ContentItemEntity
    {
        $listEntries = $this->listEntriesHelper->getEntries('contentItem', 'scope');
        $items = [];
        foreach ($listEntries as $listEntry) {
            if (true === $listEntry['default']) {
                $items[] = $listEntry['value'];
            }
        }
        $entity->setScope(implode('###', $items));

        return $entity;
    }

    public function getListEntriesHelper(): ?ListEntriesHelper
    {
        return $this->listEntriesHelper;
    }
    
    public function setListEntriesHelper(ListEntriesHelper $listEntriesHelper = null): void
    {
        if ($this->listEntriesHelper !== $listEntriesHelper) {
            $this->listEntriesHelper = $listEntriesHelper;
        }
    }
    
}
