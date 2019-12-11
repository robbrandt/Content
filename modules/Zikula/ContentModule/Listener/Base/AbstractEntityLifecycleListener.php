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

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Zikula\Common\Translator\Translator;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ExtensionsModule\Api\VariableApi;
use Zikula\UsersModule\Api\CurrentUserApi;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Listener\LoggableListener;

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

    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->setContainer($container);
        $this->eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);
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
            Events::preFlush,
            Events::onFlush,
            Events::postFlush,
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
     * The preFlush event is called at EntityManager#flush() before anything else.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#preflush
     */
    public function preFlush(PreFlushEventArgs $args): void
    {
        $this->activateCustomLoggableListener();
    }

    /**
     * The onFlush event is called inside EntityManager#flush() after the changes to all the
     * managed entities and their associations have been computed.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#onflush
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
    }

    /**
     * The postFlush event is called at the end of EntityManager#flush().
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postflush
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
    }

    /**
     * The preRemove event occurs for a given entity before the respective EntityManager
     * remove operation for that entity is executed. It is not called for a DQL DELETE statement.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#preremove
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // create the filter event and dispatch it
        $eventClass = '\\Zikula\\ContentModule\\ContentEvents';
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch($event, constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_PRE_REMOVE'));
    }

    /**
     * The postRemove event occurs for an entity after the entity has been deleted. It will be
     * invoked after the database delete operations. It is not called for a DQL DELETE statement.
     *
     * Note that the postRemove event or any events triggered after an entity removal can receive
     * an uninitializable proxy in case you have configured an entity to cascade remove relations.
     * In this case, you should load yourself the proxy in the associated pre event.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postupdate-postremove-postpersist
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        $objectType = $entity->get_objectType();
        
        $this->purgeHistory($objectType);
        
        $currentUserApi = $this->container->get(CurrentUserApi::class);
        $logArgs = ['app' => 'ZikulaContentModule', 'user' => $currentUserApi->get('uname'), 'entity' => $objectType, 'id' => $entity->getKey()];
        $this->logger->debug('{app}: User {user} removed the {entity} with id {id}.', $logArgs);
        
        // create the filter event and dispatch it
        $eventClass = '\\Zikula\\ContentModule\\ContentEvents';
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch($event, constant($eventClass . '::' . strtoupper($objectType) . '_POST_REMOVE'));
    }

    /**
     * The prePersist event occurs for a given entity before the respective EntityManager
     * persist operation for that entity is executed. It should be noted that this event
     * is only triggered on initial persist of an entity (i.e. it does not trigger on future updates).
     *
     * Doctrine will not recognize changes made to relations in a prePersist event.
     * This includes modifications to collections such as additions, removals or replacement.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#prepersist
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || (!method_exists($entity, 'get_objectType') && !$entity instanceof AbstractLogEntry)) {
            return;
        }
        
        if ($entity instanceof AbstractLogEntry) {
            // check if a supported object has been undeleted
            if ('create' !== $entity->getAction()) {
                return;
            }
        
            // select main entity
            if (null === $entity->getObjectId()) {
                return;
            }
        
            $repository = $this->container->get(EntityFactory::class)->getEntityManager()->getRepository($entity->getObjectClass());
            $object = $repository->find($entity->getObjectId());
            if (null === $object || !method_exists($object, 'get_objectType')) {
                return;
            }
        
            // set correct version after undeletion
            $logVersion = $entity->getVersion();
            if ('page' === $object->get_objectType() && method_exists($object, 'getCurrentVersion')) {
                if ($logVersion < $object->getCurrentVersion()) {
                    $entity->setVersion($object->getCurrentVersion());
                }
            }
        
            return;
        }
        
        // create the filter event and dispatch it
        $eventClass = '\\Zikula\\ContentModule\\ContentEvents';
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch($event, constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_PRE_PERSIST'));
    }

    /**
     * The postPersist event occurs for an entity after the entity has been made persistent.
     * It will be invoked after the database insert operations. Generated primary key values
     * are available in the postPersist event.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postupdate-postremove-postpersist
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        $currentUserApi = $this->container->get(CurrentUserApi::class);
        $logArgs = ['app' => 'ZikulaContentModule', 'user' => $currentUserApi->get('uname'), 'entity' => $entity->get_objectType(), 'id' => $entity->getKey()];
        $this->logger->debug('{app}: User {user} created the {entity} with id {id}.', $logArgs);
        
        $this->purgeHistory($entity->get_objectType());
        
        // create the filter event and dispatch it
        $eventClass = '\\Zikula\\ContentModule\\ContentEvents';
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch($event, constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_POST_PERSIST'));
    }

    /**
     * The preUpdate event occurs before the database update operations to entity data.
     * It is not called for a DQL UPDATE statement nor when the computed changeset is empty.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#preupdate
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // create the filter event and dispatch it
        $eventClass = '\\Zikula\\ContentModule\\ContentEvents';
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch($event, constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_PRE_UPDATE'));
    }

    /**
     * The postUpdate event occurs after the database update operations to entity data.
     * It is not called for a DQL UPDATE statement.
     *
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postupdate-postremove-postpersist
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        $currentUserApi = $this->container->get(CurrentUserApi::class);
        $logArgs = ['app' => 'ZikulaContentModule', 'user' => $currentUserApi->get('uname'), 'entity' => $entity->get_objectType(), 'id' => $entity->getKey()];
        $this->logger->debug('{app}: User {user} updated the {entity} with id {id}.', $logArgs);
        
        $this->purgeHistory($entity->get_objectType());
        
        // create the filter event and dispatch it
        $eventClass = '\\Zikula\\ContentModule\\ContentEvents';
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch($event, constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_POST_UPDATE'));
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
     * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#postload
     */
    public function postLoad(LifecycleEventArgs $args): void
    {
        /** @var EntityAccess $entity */
        $entity = $args->getObject();
        if (!$this->isEntityManagedByThisBundle($entity) || !method_exists($entity, 'get_objectType')) {
            return;
        }
        
        // create the filter event and dispatch it
        $eventClass = '\\Zikula\\ContentModule\\ContentEvents';
        $event = $this->createFilterEvent($entity);
        $this->eventDispatcher->dispatch($event, constant($eventClass . '::' . strtoupper($entity->get_objectType()) . '_POST_LOAD'));
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

    /**
     * Returns a filter event instance for the given entity.
     */
    protected function createFilterEvent(EntityAccess $entity): Event
    {
        $filterEventClass = '\\Zikula\\ContentModule\\Event\\Filter' . ucfirst($entity->get_objectType()) . 'Event';

        return new $filterEventClass($entity);
    }

    /**
     * Purges the version history as configured.
     */
    protected function purgeHistory( string$objectType = ''): void
    {
        if (!in_array($objectType, ['page'])) {
            return;
        }

        $entityManager = $this->container->get(EntityFactory::class)->getEntityManager();
        $variableApi = $this->container->get(VariableApi::class);
        $objectTypeCapitalised = ucfirst($objectType);

        $revisionHandling = $variableApi->get('ZikulaContentModule', 'revisionHandlingFor' . $objectTypeCapitalised, 'unlimited');
        $limitParameter = '';
        if ('limitedByAmount' === $revisionHandling) {
            $limitParameter = $variableApi->get('ZikulaContentModule', 'maximumAmountOf' . $objectTypeCapitalised . 'Revisions', 25);
        } elseif ('limitedByDate' === $revisionHandling) {
            $limitParameter = $variableApi->get('ZikulaContentModule', 'periodFor' . $objectTypeCapitalised . 'Revisions', 'P1Y0M0DT0H0M0S');
        }

        $logEntriesRepository = $entityManager->getRepository('ZikulaContentModule:' . $objectTypeCapitalised . 'LogEntryEntity');
        $logEntriesRepository->purgeHistory($revisionHandling, $limitParameter);
    }

    /**
     * Enables the custom loggable listener.
     */
    protected function activateCustomLoggableListener(): void
    {
        $entityManager = $this->container->get(EntityFactory::class)->getEntityManager();
        $eventManager = $entityManager->getEventManager();
        $customLoggableListener = $this->container->get(LoggableListener::class);

        $hasLoggableActivated = false;
        foreach ($eventManager->getListeners() as $event => $listeners) {
            foreach ($listeners as $hash => $listener) {
                if (is_object($listener) && 'Gedmo\Loggable\LoggableListener' === get_class($listener)) {
                    $eventManager->removeEventSubscriber($listener);
                    $hasLoggableActivated = true;
                    break 2;
                }
            }
        }

        if (!$hasLoggableActivated) {
            // translations are persisted, so we temporarily disable loggable listener
            // to avoid creating unrequired log entries for the main entity
            return;
        }

        $currentUserApi = $this->container->get(CurrentUserApi::class);
        $userName = $currentUserApi->isLoggedIn() ? $currentUserApi->get('uname') : $this->container->get(Translator::class)->__('Guest');

        $customLoggableListener->setUsername($userName);

        $eventManager->addEventSubscriber($customLoggableListener);
    }
}