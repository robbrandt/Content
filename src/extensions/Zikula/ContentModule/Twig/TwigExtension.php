<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Twig;

use Zikula\Bundle\CoreBundle\Doctrine\EntityAccess;
use Zikula\ContentModule\Twig\Base\AbstractTwigExtension;

/**
 * Twig extension implementation class.
 */
class TwigExtension extends AbstractTwigExtension
{
    public function increaseCounter(EntityAccess $entity, string $fieldName = ''): void
    {
        $countPageViews = (bool) $this->variableApi->get('ZikulaContentModule', 'countPageViews', false);
        if (!$countPageViews) {
            return;
        }

        parent::increaseCounter($entity, $fieldName);
    }
}
