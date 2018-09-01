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

namespace Zikula\ContentModule\Listener\Base;

use Gedmo\Loggable\LoggableListener as BaseListener;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\LoggableHelper;

/**
 * Event handler implementation class for injecting log entry additions.
 */
abstract class AbstractLoggableListener extends BaseListener
{
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var LoggableHelper
     */
    protected $loggableHelper;
    
    /**
     * LoggableListener constructor.
     *
     * @param EntityDisplayHelper $entityDisplayHelper EntityDisplayHelper service instance
     * @param LoggableHelper      $loggableHelper      LoggableHelper service instance
     */
    public function __construct(
        EntityDisplayHelper $entityDisplayHelper,
        LoggableHelper $loggableHelper
    ) {
        parent::__construct();
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->loggableHelper = $loggableHelper;
    }
    
    /**
     * @inheritDoc
     */
    protected function prePersistLogEntry($logEntry, $object)
    {
        parent::prePersistLogEntry($logEntry, $object);
    
        $objectType = $object->get_objectType();
    
        $versionFieldName = $this->loggableHelper->getVersionFieldName($objectType);
        $versionGetter = 'get' . ucfirst($versionFieldName);
    
        // workaround to set correct version after restore of item
        if (BaseListener::ACTION_CREATE == $logEntry->getAction() && $logEntry->getVersion() < $object->$versionGetter()) {
            $logEntry->setVersion($object->$versionGetter());
        }
    
        if (!method_exists($logEntry, 'setActionDescription')) {
            return;
        }
    
        if (BaseListener::ACTION_REMOVE == $logEntry->getAction()) {
            // provide title to make the object identifiable in the list of deleted entities
            $logEntry->setActionDescription($this->entityDisplayHelper->getFormattedTitle($object));
    
            return;
        }
    
        if (method_exists($object, 'get_actionDescriptionForLogEntry')) {
            $logEntry->setActionDescription($object->get_actionDescriptionForLogEntry());
        }
        if (!$logEntry->getActionDescription()) {
            // treat all changes without an explicit description as update
            $logEntry->setActionDescription('_HISTORY_' . strtoupper($objectType) . '_UPDATED');
        }
    }
}