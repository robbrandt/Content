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
 * Heading content type.
 */
class HeadingType extends AbstractContentType
{
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_BASIC;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'underline';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Heading');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Section heading (or page title) for structuring large amounts of text.');
    }

    /**
     * @inheritDoc
     */
    public function isTranslatable()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'text' => $this->__('Heading'), 
            'headingType' => 'h3', 
            'anchorName' => '', 
            'displayPageTitle' => false
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->data['text']));
    }

    /**
     * @inheritDoc
     */
    public function displayView()
    {
        if (true === $this->data['displayPageTitle']) {
            $this->data['text'] = $this->getEntity()->getPage()->getTitle();
        }

        return parent::displayView();
    }

    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return ''; // TODO
    }
}