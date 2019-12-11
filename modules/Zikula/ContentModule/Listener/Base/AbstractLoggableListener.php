<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Listener\Base;

use Gedmo\Loggable\LoggableListener as BaseListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Zikula\Core\Doctrine\EntityAccess;
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
    
    public function __construct(
        EntityDisplayHelper $entityDisplayHelper,
        LoggableHelper $loggableHelper
    ) {
        parent::__construct();
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->loggableHelper = $loggableHelper;
    }
    
    protected function prePersistLogEntry($logEntry, $object)
    {
        /** @var EntityAccess $object */
        if (!$this->isEntityManagedByThisBundle($object) || !method_exists($object, 'get_objectType')) {
            return;
        }
    
        parent::prePersistLogEntry($logEntry, $object);
    
        $objectType = $object->get_objectType();
    
        $versionFieldName = $this->loggableHelper->getVersionFieldName($objectType);
        $versionGetter = 'get' . ucfirst($versionFieldName);
    
        // workaround to set correct version after restore of item
        if (
            BaseListener::ACTION_CREATE === $logEntry->getAction()
            && $logEntry->getVersion() < $object->$versionGetter()
        ) {
            $logEntry->setVersion($object->$versionGetter());
        }
    
        if (!method_exists($logEntry, 'setActionDescription')) {
            return;
        }
    
        if (BaseListener::ACTION_REMOVE === $logEntry->getAction()) {
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
    
    protected function createLogEntry($action, $object, LoggableAdapter $ea)
    {
        if (!$this->isEntityManagedByThisBundle($object) || !method_exists($object, 'get_objectType')) {
            return;
        }
    
        return parent::createLogEntry($action, $object, $ea);
    }
    
    /**
     * Checks whether this listener is responsible for the given entity or not.
     *
     * @param EntityAccess $entity The given entity
     */
    protected function isEntityManagedByThisBundle($entity): bool
    {
        $entityClassParts = explode('\\', get_class($entity));
    
        if ('DoctrineProxy' === $entityClassParts[0] && '__CG__' === $entityClassParts[1]) {
            array_shift($entityClassParts);
            array_shift($entityClassParts);
        }
    
        return 'Zikula' === $entityClassParts[0] && 'ContentModule' === $entityClassParts[1];
    }
}