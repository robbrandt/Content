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

namespace Zikula\ContentModule\ContentType;

/**
 * Join position content type.
 */
class JoinPositionType extends AbstractContentType
{
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_EXPERT;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'retweet';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Join Position');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Joins different positions, e.g. clearing left, right or both sides; can be used to fix the layout / text flow.');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'clear' => 'both'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return ''; // TODO
    }
}