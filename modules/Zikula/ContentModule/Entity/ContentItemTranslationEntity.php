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

namespace Zikula\ContentModule\Entity;

use Zikula\ContentModule\Entity\Base\AbstractContentItemTranslationEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity extension domain class storing content item translations.
 *
 * This is the concrete translation class for content item entities.
 *
 * @ORM\Entity(repositoryClass="Zikula\ContentModule\Entity\Repository\ContentItemTranslationRepository")
 * @ORM\Table(
 *     name="zikula_content_contentitem_translation",
 *     options={"row_format":"DYNAMIC"},
 *     indexes={
 *         @ORM\Index(name="translations_lookup_idx", columns={
 *             "locale", "object_class", "foreign_key"
 *         })
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *             "locale", "object_class", "field", "foreign_key"
 *         })
 *     }
 * )
 */
class ContentItemTranslationEntity extends BaseEntity
{
    // feel free to add your own methods here
}
