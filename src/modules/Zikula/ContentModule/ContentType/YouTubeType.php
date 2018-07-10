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
use Zikula\ContentModule\ContentType\Form\Type\YouTubeType as FormType;

/**
 * YouTube content type.
 */
class YouTubeType extends AbstractContentType
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
        return 'youtube';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('YouTube video');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Display a YouTube video clip.');
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
            'url' => '',
            // TODO remove this?
            //'width' => 320,
            //'height' => 240,
            'text' => '',
            'videoId' => '',
            'displayMode' => 'inline',
            'videoMode' => 'HTML5',
            'showRelated' => false,
            'autoplay' => false
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
        if ($this->videoId == $this->url) {
            // may happen when using translations
            $r = '/\?v=([-a-zA-Z0-9_]+)(&|$)/';
            if (preg_match($r, $this->url, $matches)) {
                $this->videoId = $matches[1];
            }
        }
        $this->view->assign('showRelated', ($this->showRelated ? '1' : '0'));
        $this->view->assign('autoplay', ($this->autoplay ? '1' : '0'));

        return $this->view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        $output = '<div style="background-color:Lavender; width:' . $this->width . 'px; height:' . $this->height . 'px; margin:0 auto; padding:10px;">' . $this->__f('<strong>Video-ID : %1$s</strong><br />Size in pixels: %2$s x %3$s', array($this->videoId, $this->width, $this->height));
        $output .= '<br />' . ($this->videoMode == 'HTML5' ? $this->__('Default HTML5 embedding code used') : $this->__('Legacy Flash embedding code used'));
        $output .= '<br />' . ($this->showRelated ? $this->__('Related videos are shown') : $this->__('Related videos not shown'));
        $output .= $this->autoplay ? '<br />'.$this->__('Video is autoplayed') : '';
        $output .= '</div>';
        $output .= '<p style="width:' . $this->width . 'px; margin:0 auto;">' . DataUtil::formatForDisplay($this->text) . '</p>';
        return $output;
    }
    function isValid(&$data)
    {
        $r = '/\?v=([-a-zA-Z0-9_]+)(&|$)/';
        if (preg_match($r, $data['url'], $matches)) {
            $this->videoId = $data['videoId'] = $matches[1];
        }
        if (empty($this->videoId)) {
            return $this->view->setPluginErrorMsg('url', 'Value of url not valid');
        }
        if (empty($data['width']) || !is_numeric($data['width'])) {
            return $this->view->setPluginErrorMsg('width', 'Value of width not valid');
        }
        if (empty($data['height']) || !is_numeric($data['height'])) {
            return $this->view->setPluginErrorMsg('width', 'Value of height not valid');
        }
        return true;
    }
*/
    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return FormType::class;
    }
}
