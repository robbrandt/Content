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

use Zikula\ContentModule\Entity\Base\AbstractPageLogEntryEntity as BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity extension domain class storing page log entries.
 *
 * This is the concrete log entry class for page entities.
 *
 * @ORM\Entity(repositoryClass="Zikula\ContentModule\Entity\Repository\PageLogEntryRepository")
 * @ORM\Table(
 *     name="zikula_content_page_log_entry",
 *     options={"row_format":"DYNAMIC"},
 *     indexes={
 *         @ORM\Index(name="log_class_lookup_idx", columns={"object_class"}),
 *         @ORM\Index(name="log_date_lookup_idx", columns={"logged_at"}),
 *         @ORM\Index(name="log_user_lookup_idx", columns={"username"}),
 *         @ORM\Index(name="log_version_lookup_idx", columns={"object_id", "object_class", "version"}),
 *         @ORM\Index(name="log_object_id_lookup_idx", columns={"object_id"})
 *     }
 * )
 */
class PageLogEntryEntity extends BaseEntity
{
    // feel free to add your own methods here
}