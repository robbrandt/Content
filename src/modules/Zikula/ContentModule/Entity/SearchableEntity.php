<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Entity;

use Zikula\ContentModule\Entity\Base\AbstractSearchableEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity class that defines the entity structure and behaviours.
 *
 * This is the concrete entity class for searchable entities.
 * @ORM\Entity(repositoryClass="Zikula\ContentModule\Entity\Repository\SearchableRepository")
 * @ORM\Table(name="zikula_content_searchable",
 *     indexes={
 *         @ORM\Index(name="workflowstateindex", columns={"workflowState"})
 *     }
 * )
 */
class SearchableEntity extends BaseEntity
{
    // feel free to add your own methods here
}
