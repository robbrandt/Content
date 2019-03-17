<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Form\DataTransformer\Base;

use Symfony\Component\Form\DataTransformerInterface;
use Zikula\ContentModule\Helper\ListEntriesHelper;

/**
 * List field transformer base class.
 *
 * This data transformer treats multi-valued list fields.
 */
abstract class AbstractListFieldTransformer implements DataTransformerInterface
{
    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * ListFieldTransformer constructor.
     *
     * @param ListEntriesHelper $listHelper
     */
    public function __construct(ListEntriesHelper $listHelper)
    {
        $this->listHelper = $listHelper;
    }

    /**
     * Transforms the object values to the normalised value.
     *
     * @param string|null $values The object values
     *
     * @return array Normalised value
     */
    public function transform($values)
    {
        if (null === $values || '' === $values) {
            return [];
        }

        if (is_array($values)) {
            return $values;
        }

        return $this->listHelper->extractMultiList($values);
    }

    /**
     * Transforms an array with values back to the string.
     *
     * @param array $values The values
     *
     * @return string Resulting string
     */
    public function reverseTransform($values)
    {
        if (!$values) {
            return '';
        }

        return '###' . implode('###', $values) . '###';
    }
}
