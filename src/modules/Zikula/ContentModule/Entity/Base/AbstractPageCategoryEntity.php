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

namespace Zikula\ContentModule\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Zikula\CategoriesModule\Entity\AbstractCategoryAssignment;

/**
 * Entity extension domain class storing page categories.
 *
 * This is the base category class for page entities.
 */
abstract class AbstractPageCategoryEntity extends AbstractCategoryAssignment
{
    /**
     * @ORM\ManyToOne(targetEntity="\Zikula\ContentModule\Entity\PageEntity", inversedBy="categories")
     * @ORM\JoinColumn(name="entityId", referencedColumnName="id")
     * @var \Zikula\ContentModule\Entity\PageEntity
     */
    protected $entity;
    
    /**
     * Get reference to owning entity.
     *
     * @return \Zikula\ContentModule\Entity\PageEntity
     */
    public function getEntity()
    {
        return $this->entity;
    }
    
    /**
     * Set reference to owning entity.
     *
     * @param \Zikula\ContentModule\Entity\PageEntity $entity
     */
    public function setEntity(/*\Zikula\ContentModule\Entity\PageEntity */$entity)
    {
        $this->entity = $entity;
    }
}
