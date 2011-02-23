<?php
/**
 * Content Slideshare plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_Slideshare extends Content_ContentType
{
    var $url;
    var $text;
    var $slideId;
    var $playerType;
    var $width;
    var $height;

    function getTitle()
    {
        return $this->__('Slideshare');
    }
    function getDescription()
    {
        return $this->__('Display slides from slideshare.com');
    }
    function isTranslatable()
    {
        return true;
    }
    function loadData(&$data)
    {
        $this->url = $data['url'];
        $this->text = $data['text'];
        $this->slideId = $data['slideId'];
        $this->playerType = $data['playerType'];
        $this->width = $data['width'];
        $this->height = $data['height'];
    }
    function display()
    {
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('url', $this->url);
        $view->assign('text', $this->text);
        $view->assign('slideId', $this->slideId);
        $view->assign('playerType', $this->playerType);
        $view->assign('width', $this->width);
        $view->assign('height', $this->height);

        return $view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        $output = '<div style="background-color:#ddd; width:320px; height:200px; margin:0 auto; padding:15px;">' . $this->__f('Slideshare: %s', $this->slideId) . '</div>';
        $output .= '<p style="width:320px; margin:0 auto;">' . DataUtil::formatForDisplay($this->text) . '</p>';
        return $output;
    }
    function getDefaultData()
    {
        return array('url' => '', 'text' => '', 'slideId' => '', 'playerType' => '0', 'width' => 425, 'height' => 355);
    }
    function isValid(&$data)
    {
        // [slideshare id=3318451&doc=rainfallreport-100302124103-phpapp02&type=d]
        // type=d is optional and player ssplayerd.swf should be used instead of the default one
        // Old expression without type=d $r = '/^[slideshare id=[0-9]+\&doc=([^&]+?)\]/';
        $r = '/^[slideshare id=[0-9]+\&doc=([^&]+?)\&([^&]+)\]/';
        if (preg_match($r, $data['url'], $matches)) {
            $this->slideId = $data['slideId'] = $matches[1];
            if (!empty($matches[2]) && $matches[2]=='type=d') {
                $this->playerType = $data['playerType'] = 1;
            } else {
                $this->playerType = $data['playerType'] = 0;
            }
            return true;
        }
        return false;
    }
}