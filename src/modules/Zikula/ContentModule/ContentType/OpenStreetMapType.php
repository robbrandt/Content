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

use Zikula\ContentModule\AbstractContentType;
use Zikula\ContentModule\ContentTypeInterface;
use Zikula\ContentModule\ContentType\Form\Type\OpenStreetMapType as FormType;

/**
 * Open street map content type.
 */
class OpenStreetMapType extends AbstractContentType
{
    /**
     * @inheritDoc
     */
    public function getCategory()
    {
        return ContentTypeInterface::CATEGORY_EXTERNAL;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'map-pin';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('OpenStreetMap map');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Display OpenStreetMap map position.');
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
            'latitude' => '55.8756960390043',
            'longitude' => '12.36185073852539',
            'zoom' => 5,
            'height' => 400,
            'text' => ''
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->data['text']));
    }

/** TODO
    function display()
    {
        $this->view->assign('zoom', $this->zoom);
        $this->view->assign('height', $this->height);
        $this->view->assign('text', DataUtil::formatForDisplayHTML($this->text));
        $this->view->assign('contentId', $this->contentId);
        $this->view->assign('language', ZLanguage::getLanguageCode());

        return $this->view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        return DataUtil::formatForDisplay($this->text);
    }
*/
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

        if (in_array($context, [ContentTypeInterface::CONTEXT_VIEW, ContentTypeInterface::CONTEXT_EDIT])) {
            $assets['js'][] = 'https://openlayers.org/api/OpenLayers.js';
            $assets['js'][] = 'https://www.openstreetmap.org/openlayers/OpenStreetMap.js';
            $assets['js'][] = $this->assetHelper->resolve('@ZikulaContentModule:js/ZikulaContentModule.ContentType.OpenStreetMap.js');
        }

        return $assets;
    }

    /**
     * @inheritDoc
     */
    public function getJsEntrypoint($context)
    {
        if (ContentTypeInterface::CONTEXT_VIEW == $context) {
            return 'contentInitOsmDisplay';
        }
        if (ContentTypeInterface::CONTEXT_EDIT == $context) {
            return 'contentInitOsmEdit';
        }

        return null;
    }
}
