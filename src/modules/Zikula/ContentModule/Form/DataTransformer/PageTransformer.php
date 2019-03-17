<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Entity\PageEntity;

/**
 * Page transformer class.
 *
 * This data transformer treats page identifiers and entities.
 */
class PageTransformer implements DataTransformerInterface
{
    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * PageTransformer constructor.
     *
     * @param EntityFactory $entityFactory
     */
    public function __construct(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    /**
     * Transforms the object values to the normalised value.
     *
     * @param PageEntity|integer|null $value The object values
     *
     * @return integer Normalised value
     */
    public function transform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (is_numeric($value)) {
            return $value > 0 ? $this->entityFactory->getRepository('page')->selectById($value, false) : null;
        }

        return $value;
    }

    /**
     * Transforms a page entity back to the identifier.
     *
     * @param PageEntity $value The page
     *
     * @return integer The page identifier
     */
    public function reverseTransform($value)
    {
        if (!$value) {
            return 0;
        }

        if ($value instanceof PageEntity) {
            return $value->getId();
        }

        return intval($value);
    }
}
