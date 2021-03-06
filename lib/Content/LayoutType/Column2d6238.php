<?php
/**
 * Content 2 column layout plugin
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

class Content_LayoutType_Column2d6238 extends Content_AbstractLayoutType
{
    protected $templateType = 1;

    function __construct(Zikula_View $view)
    {
        parent::__construct($view);
        $this->contentAreaTitles = array(
            $this->__('Header'),
            $this->__('Left column'),
            $this->__('Right column'),
            $this->__('Footer'));
    }
    function getTitle()
    {
        return $this->__('2 columns (62|38)');
    }
    function getDescription()
    {
        return $this->__('Header + two columns (62|38) + footer');
    }
    function getNumberOfContentAreas()
    {
        return 4;
    }
    function getImage()
    {
        return System::getBaseUrl().'/modules/Content/images/layouttype/column2_6238_header.png';
    }
}