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

namespace Zikula\ContentModule\Helper;

use Zikula\ContentModule\Helper\Base\AbstractViewHelper;

/**
 * Helper implementation class for view layer methods.
 */
class ViewHelper extends AbstractViewHelper
{
    protected function determineExtension(string $type, string $func): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            $format = $request->getRequestFormat();
            if (in_array($format, ['htm', 'phtml', 'shtml'], true)) {
                $request->setRequestFormat('html');
            }
        }

        return parent::determineExtension($type, $func);
    }
}
