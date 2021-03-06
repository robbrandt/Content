<?php
/**
 * Content YouTube plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_ContentType_YouTube extends Content_AbstractContentType
{
    protected $url;
    protected $width;
    protected $height;
    protected $text;
    protected $videoId;
    protected $displayMode;
    protected $videoMode;
    protected $showRelated;
    protected $autoplay;

    // Get and Set methods for class properties
    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getWidth()
    {
        return $this->width;
    }
    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getHeight()
    {
        return $this->height;
    }
    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getText()
    {
        return $this->text;
    }
    public function setText($text)
    {
        $this->text = $text;
    }

    public function getVideoId()
    {
        return $this->videoId;
    }
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
    }

    public function getDisplayMode()
    {
        return $this->displayMode;
    }
    public function setDisplayMode($displayMode)
    {
        $this->displayMode = $displayMode;
    }

    public function getVideoMode()
    {
        return $this->videoMode;
    }
    public function setVideoMode($videoMode)
    {
        $this->videoMode = $videoMode;
    }

    public function getShowRelated()
    {
        return $this->showRelated;
    }
    public function setShowRelated($showRelated)
    {
        $this->showRelated = $showRelated;
    }
    
    public function getAutoplay()
    {
        return $this->autoplay;
    }
    public function setAutoplay($autoplay)
    {
        $this->autoplay = $autoplay;
    }
    
    // inherited methods
    function getTitle()
    {
        return $this->__('YouTube video clip');
    }
    function getDescription()
    {
        return $this->__('Display YouTube video clip.');
    }
    function isTranslatable()
    {
        return true;
    }
    function loadData(&$data)
    {
        $this->url = $data['url'];
        $this->width = $data['width'];
        $this->height = $data['height'];
        $this->text = $data['text'];
        $this->videoId = $data['videoId'];
        $this->displayMode = isset($data['displayMode']) ? $data['displayMode'] : 'inline';
        $this->videoMode = isset($data['videoMode']) ? $data['videoMode'] : 'HTML5';
        $this->showRelated = isset($data['showRelated']) ? $data['showRelated'] : 0;
        $this->autoplay = isset($data['autoplay']) ? $data['autoplay'] : 0;
    }
    function display()
    {
        $this->view->assign('url', $this->url);
        $this->view->assign('width', $this->width);
        $this->view->assign('height', $this->height);
        $this->view->assign('text', $this->text);
        $this->view->assign('videoId', $this->videoId);
        $this->view->assign('displayMode', $this->displayMode);
        $this->view->assign('videoMode', $this->videoMode);
        $this->view->assign('showRelated', ($this->showRelated ? '1' : '0'));
        $this->view->assign('autoplay', ($this->autoplay ? '1' : '0'));

        return $this->view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        $output = '<div style="background-color:Lavender; width:' . $this->width . 'px; height:' . $this->height . 'px; margin:0 auto; padding:10px;">' . $this->__f('<strong>Video-ID : %1$s</strong><br />Size in pixels: %2$s x %3$s', array($this->videoId, $this->width, $this->height));
        $output .= '<br />' . ($this->videoMode == 'HTML5' ? $this->__('Default HTML5 embedding code used') : $this->__('Legacy Flash embedding code used'));
        $output .= '<br />' . ($this->showRelated == 1 ? $this->__('Related videos are shown') : $this->__('Related videos not shown'));
        $output .= $this->autoplay == 1 ? '<br />'.$this->__('Video is autoplayed') : '';
        $output .= '</div>';
        $output .= '<p style="width:' . $this->width . 'px; margin:0 auto;">' . DataUtil::formatForDisplay($this->text) . '</p>';
        return $output;
    }
    function getDefaultData()
    {
        return array('url' => '', 'width' => '640', 'height' => '360', 'text' => '', 'videoId' => '', 'displayMode' => 'inline', 'videoMode' => 'HTML5', 'showRelated' => '0', 'autoplay' => '0');
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
}
