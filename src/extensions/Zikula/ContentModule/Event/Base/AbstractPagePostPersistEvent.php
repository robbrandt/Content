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

namespace Zikula\ContentModule\Event\Base;

use Zikula\ContentModule\Entity\PageEntity;

/**
 * Event base class for filtering page processing.
 */
abstract class AbstractPagePostPersistEvent
{
    /**
     * @var PageEntity Reference to treated entity instance.
     */
    protected $page;

    public function __construct(PageEntity $page)
    {
        $this->page = $page;
    }

    /**
     * @return PageEntity
     */
    public function getPage(): PageEntity
    {
        return $this->page;
    }
}
