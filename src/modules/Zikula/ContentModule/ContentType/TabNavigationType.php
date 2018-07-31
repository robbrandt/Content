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
use Zikula\ContentModule\ContentType\Form\Type\TabNavigationType as FormType;

/**
 * Tab navigation content type.
 */
class TabNavigationType extends AbstractContentType
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
        return 'columns';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Tab navigation');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Tab navigation with existing Content items.');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'contentItemIds' => '',
            'tabTitles' => '',
            'tabLinks' => '',
            'tabType' => '',
            'tabStyle' => ''
        ];
    }

/** TODO
    public function display()
    {
        // Convert the variables into arrays
        $contentItemIds = explode(';', str_replace(' ', '', $this->contentItemIds));
        $tabTitles = explode(';', $this->tabTitles);
        $tabLinks = explode(';', str_replace(' ', '', $this->tabLinks));

        // Make an array with output display of the Content items to tab
        $itemsToTab = array();
        foreach ($contentItemIds as $key => $contentItemId) {
            if (($contentItem = ModUtil::apiFunc('Content', 'Content', 'getContent', array('id' => $contentItemId))) != false) {
                $itemsToTab[$key]['display'] = $contentItem['plugin']->displayStart() . $contentItem['plugin']->display() . $contentItem['plugin']->displayEnd();
                $itemsToTab[$key]['title'] = $tabTitles[$key];
                $itemsToTab[$key]['link'] = isset($tabLinks[$key]) ? $tabLinks[$key] : 'tab'.$key;
            }
        }

        // assign variables and call the template
        $this->view->assign('itemsToTab', $itemsToTab);
        $this->view->assign('tabType', $this->tabType);
        $this->view->assign('tabStyle', $this->tabStyle);
        $this->view->assign('contentId', $this->contentId);
        return $this->view->fetch($this->getTemplate());
    }

    public function displayEditing()
    {
        $output = '<h3>' . $this->__f('Tab navigation of Content items %s', $this->contentItemIds) . '</h3>';
        $output .= '<p>';
        switch($this->tabType) {
            case 1:
            $output .= $this->__('Tab navigation type') . ': ' . $this->__('Bootstrap - nav nav-tabs');
            break;
            case 2:
            $output .= $this->__('Tab navigation type') . ': ' . $this->__('Bootstrap - nav nav-pills');
            break;
            case 3:
            $output .= $this->__('Tab navigation type') . ': ' . $this->__('Bootstrap - nav nav-pills nav-stacked (col-sm3/col-sm-9)');
            break;
        }
        $output .= '<br />' . $this->__('You can disable the individual Content Items if you only want to display them in this Tab Navigation.');
        $output .= '</p>';
        return $output;
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
