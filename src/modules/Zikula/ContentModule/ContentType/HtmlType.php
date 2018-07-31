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

namespace Zikula\ContentModule\ContentType;

use Zikula\ContentModule\AbstractContentType;
use Zikula\ContentModule\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\HtmlType as FormType;

/**
 * HTML content type.
 */
class HtmlType extends AbstractContentType
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
        return 'font';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('HTML text');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('HTML editor for adding markup text to your page.');
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
            'text' => $this->__('Add text here...')
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
    public function getEditFormClass()
    {
        return FormType::class;
    }

    /**
     * @inheritDoc
     */
    public function getAssets($context)
    {
        $assets = parent::getAssets($context);
        if (ContentTypeInterface::CONTEXT_EDIT != $context) {
            return $assets;
        }

        $assets['js'][] = $this->assetHelper->resolve('@ZikulaContentModule:js/ZikulaContentModule.ContentType.Html.js');

        return $assets;
    }

    /**
     * @inheritDoc
     */
    public function getJsEntrypoint($context)
    {
        if (ContentTypeInterface::CONTEXT_EDIT != $context) {
            return null;
        }

        return 'contentInitHtmlEdit';
    }
}
