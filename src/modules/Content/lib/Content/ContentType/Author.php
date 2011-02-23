<?php
/**
 * Content author plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @license See license.txt
 */

class Content_ContentType_Author extends Content_ContentType
{
    var $uid;

    function getTitle()
    {
        return $this->__('Author Infobox');
    }
    function getDescription()
    {
        return $this->__('Various information about the author of the page.');
    }
    function isTranslatable()
    {
        return false;
    }
    function loadData(&$data)
    {
        $this->uid = $data['uid'];
    }
    function display()
    {
        $view = Zikula_View::getInstance('Content', false);
        $view->assign('uid', DataUtil::formatForDisplayHTML($this->uid));
        $view->assign('contentId', $this->contentId);
        return $view->fetch($this->getTemplate());
    }
    function displayEditing()
    {
        return "<h3>" . UserUtil::getVar('uname', $this->uid) . "</h3>";
    }
    function getDefaultData()
    {
        return array('uid' => '1');
    }
    function getSearchableText()
    {
        return html_entity_decode(strip_tags(UserUtil::getVar($this->uid, 'uname')));
    }
}