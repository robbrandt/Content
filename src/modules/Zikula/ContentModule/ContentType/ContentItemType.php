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
 * Content item content type.
 */
class ContentItemType extends AbstractContentType
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
        return 'link';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Existing content');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Reference and display an already existing content item.');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'contentItemId' => 0
        ];
    }

/** TODO
    protected $contentitemid;
    protected $contentitemtype;
    protected $contentitemmod;
    protected $contentitempage;
    
    function loadData(&$data)
    {
        $this->contentitemid = (int) $data['contentitemid'];

        // retieve some additional info on the content item
        if ($this->contentitemid > 0) {
            $contentitem = ModUtil::apiFunc('Content', 'Content', 'getContent', array('id' => $this->contentitemid));
            $this->contentitemtype = $contentitem['type'];
            $this->contentitemmod = $contentitem['module'];
            $this->contentitempage = $contentitem['pageId'];
        }
    }
    function display()
    {
        // retrieve the content item and return the output via the plugin display function
        $contentItem = ModUtil::apiFunc('Content', 'Content', 'getContent', array('id' => $this->contentitemid));
        
        if ($contentItem != false) {
            $output = $contentItem['plugin']->displayStart();
            $output .= $contentItem['plugin']->display();
            $output .= $contentItem['plugin']->displayEnd();
        } else {
            $output = '';
        }
        
        return $output;
    }
    function displayEditing()
    {
        $output = $this->__f('Displays existing Content Item [ID %1$s], Type: %2$s, Module %3$s, Source Page %4$s', array($this->contentitemid, $this->contentitemtype, $this->contentitemmod, $this->contentitempage));
        return $output;
    }
    function getDefaultData()
    {
        return array(
            'contentitemid' => '', 
            'contentitemtype' => '', 
            'contentitemmod' => '',
            'contentitempage' => '');
    }
*/
    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return ''; // TODO
    }
}