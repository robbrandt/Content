<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.1 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Listener\Base;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ContentModule\ContentEvents;
use Zikula\ContentModule\Event\FilterPageEvent;
use Zikula\ContentModule\Event\FilterContentItemEvent;
use Zikula\ContentModule\Event\FilterSearchableEvent;

/**
 * Event subscriber base class for entity lifecycle events.
 */
abstract class AbstractEntityLifecycleListener implements EventSubscriber, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * EntityLifecycleListener constructor.
     *
     * @param ContainerInterface       $container
     * @param EventDispatcherInterface $eventDispatcher EventDispatcher service instance
     * @param LoggerInterface          $logger          Logger service instance
     */
    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->setContainer($container);
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * Returns list of events to subscribe.
     *
     * @return string[] List of events
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
            Events::postRemove,
            Events::prePersist,
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::postLoad
        ];
    }

    /**
     * The preRemove event occurs for a given entity before the respective EntityManager
     * remove operation for that entity is executed. It is not called for a DQL DELETE statement.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // create the filter event and dispatch it
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch(constant('\\Zikula\\ContentModule\\ContentEvents::' . strtoupper($entity->get_objectType()) . '_PRE_REMOVE'), $event);
        if ($event->isPropagationStopped()) {
            return false;
        }
    }

    /**
     * The postRemove event occurs for an entity after the entity has been deleted. It will be
     * invoked after the database delete operations. It is not called for a DQL DELETE statement.
     *
     * Note that the postRemove event or any events triggered after an entity removal can receive
     * an uninitializable proxy in case you have configured an entity to cascade remove relations.
     * In this case, you should load yourself the proxy in the associated pre event.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        $objectType = $entity->get_objectType();
        
        $currentUserApi = $this->container->get('zikula_users_module.current_user');
        $logArgs = ['app' => 'ZikulaContentModule', 'user' => $currentUserApi->get('uname'), 'entity' => $objectType, 'id' => $entity->getKey()];
        $this->logger->debug('{app}: User {user} removed the {entity} with id {id}.', $logArgs);
        
        // create the filter event and dispatch it
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch(constant('\\Zikula\\ContentModule\\ContentEvents::' . strtoupper($objectType) . '_POST_REMOVE'), $event);
    }

    /**
     * The prePersist event occurs for a given entity before the respective EntityManager
     * persist operation for that entity is executed. It should be noted that this event
     * is only triggered on initial persist of an entity (i.e. it does not trigger on future updates).
     *
     * Doctrine will not recognize changes made to relations in a prePersist event.
     * This includes modifications to collections such as additions, removals or replacement.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || (!method_exists($entity, 'get_objectType') && !$entity instanceof AbstractLogEntry)) {
            return;
        }

        if ($entity instanceof AbstractLogEntry) {
            // check if a supported object has been undeleted
            if ('create' != $entity->getAction()) {
                return;
            }

            // select main entity
            if (null === $entity->getObjectId()) {
                return;
            }

            $repository = $this->container->get('doctrine.entitymanager')->getRepository($entity->getObjectClass());
            $object = $repository->find($entity->getObjectId());
            if (null === $object || !method_exists($object, 'get_objectType')) {
                return;
            }

            // set correct version after undeletion
            $logVersion = $entity->getVersion();
            if ($object->get_objectType() == 'page' && method_exists($object, 'getCurrentVersion')) {
                if ($logVersion < $object->getCurrentVersion()) {
                    $entity->setVersion($object->getCurrentVersion());
                }
            }

            return;
        }
        
        // create the filter event and dispatch it
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch(constant('\\Zikula\\ContentModule\\ContentEvents::' . strtoupper($entity->get_objectType()) . '_PRE_PERSIST'), $event);
        if ($event->isPropagationStopped()) {
            return false;
        }
    }

    /**
     * The postPersist event occurs for an entity after the entity has been made persistent.
     * It will be invoked after the database insert operations. Generated primary key values
     * are available in the postPersist event.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        $currentUserApi = $this->container->get('zikula_users_module.current_user');
        $logArgs = ['app' => 'ZikulaContentModule', 'user' => $currentUserApi->get('uname'), 'entity' => $entity->get_objectType(), 'id' => $entity->getKey()];
        $this->logger->debug('{app}: User {user} created the {entity} with id {id}.', $logArgs);
        
        // create the filter event and dispatch it
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch(constant('\\Zikula\\ContentModule\\ContentEvents::' . strtoupper($entity->get_objectType()) . '_POST_PERSIST'), $event);
    }

    /**
     * The preUpdate event occurs before the database update operations to entity data.
     * It is not called for a DQL UPDATE statement nor when the computed changeset is empty.
     *
     * @see http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#preupdate
     *
     * @param PreUpdateEventArgs $args Event arguments
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // create the filter event and dispatch it
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch(constant('\\Zikula\\ContentModule\\ContentEvents::' . strtoupper($entity->get_objectType()) . '_PRE_UPDATE'), $event);
        if ($event->isPropagationStopped()) {
            return false;
        }
    }

    /**
     * The postUpdate event occurs after the database update operations to entity data.
     * It is not called for a DQL UPDATE statement.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        $currentUserApi = $this->container->get('zikula_users_module.current_user');
        $logArgs = ['app' => 'ZikulaContentModule', 'user' => $currentUserApi->get('uname'), 'entity' => $entity->get_objectType(), 'id' => $entity->getKey()];
        $this->logger->debug('{app}: User {user} updated the {entity} with id {id}.', $logArgs);
        
        // create the filter event and dispatch it
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch(constant('\\Zikula\\ContentModule\\ContentEvents::' . strtoupper($entity->get_objectType()) . '_POST_UPDATE'), $event);
    }

    /**
     * The postLoad event occurs for an entity after the entity has been loaded into the current
     * EntityManager from the database or after the refresh operation has been applied to it.
     *
     * Note that, when using Doctrine\ORM\AbstractQuery#iterate(), postLoad events will be executed
     * immediately after objects are being hydrated, and therefore associations are not guaranteed
     * to be initialized. It is not safe to combine usage of Doctrine\ORM\AbstractQuery#iterate()
     * and postLoad event handlers.
     *
     * @param LifecycleEventArgs $args Event arguments
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // create the filter event and dispatch it
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch(constant('\\Zikula\\ContentModule\\ContentEvents::' . strtoupper($entity->get_objectType()) . '_POST_LOAD'), $event);
    }

    /**
     * Checks whether this listener is responsible for the given entity or not.
     *
     * @param EntityAccess $entity The given entity
     *
     * @return boolean True if entity is managed by this listener, false otherwise
     */
    protected function isEntityManagedByThisBundle($entity)
    {
        if (!($entity instanceof EntityAccess || $entity instanceof AbstractLogEntry)) {
            return false;
        }

        $entityClassParts = explode('\\', get_class($entity));

        return ($entityClassParts[0] == 'Zikula' && $entityClassParts[1] == 'ContentModule');
    }

    /**
     * Returns a filter event instance for the given entity.
     *
     * @param EntityAccess $entity The given entity
     *
     * @return Event The created event instance
     */
    protected function createFilterEvent($entity)
    {
        $filterEventClass = '\\Zikula\\ContentModule\\Event\\Filter' . ucfirst($entity->get_objectType()) . 'Event';
        $event = new $filterEventClass($entity);

        return $event;
    }
}
