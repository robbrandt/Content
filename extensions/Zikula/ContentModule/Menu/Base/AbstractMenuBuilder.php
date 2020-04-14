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

namespace Zikula\ContentModule\Menu\Base;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\ContentEvents;
use Zikula\ContentModule\Event\ItemActionsMenuPostConfigurationEvent;
use Zikula\ContentModule\Event\ItemActionsMenuPreConfigurationEvent;
use Zikula\ContentModule\Event\ViewActionsMenuPostConfigurationEvent;
use Zikula\ContentModule\Event\ViewActionsMenuPreConfigurationEvent;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\LoggableHelper;
use Zikula\ContentModule\Helper\ModelHelper;
use Zikula\ContentModule\Helper\PermissionHelper;

/**
 * Menu builder base class.
 */
class AbstractMenuBuilder
{
    /**
     * @var FactoryInterface
     */
    protected $factory;
    
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var LoggableHelper
     */
    protected $loggableHelper;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var ModelHelper
     */
    protected $modelHelper;
    
    public function __construct(
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        EntityDisplayHelper $entityDisplayHelper,
        LoggableHelper $loggableHelper,
        CurrentUserApiInterface $currentUserApi,
        VariableApiInterface $variableApi,
        ModelHelper $modelHelper
    ) {
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->permissionHelper = $permissionHelper;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->loggableHelper = $loggableHelper;
        $this->currentUserApi = $currentUserApi;
        $this->variableApi = $variableApi;
        $this->modelHelper = $modelHelper;
    }
    
    /**
     * Builds the item actions menu.
     */
    public function createItemActionsMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('itemActions');
        if (!isset($options['entity'], $options['area'], $options['context'])) {
            return $menu;
        }
    
        $entity = $options['entity'];
        $routeArea = $options['area'];
        $context = $options['context'];
    
        // return empty menu for preview of deleted items
        $routeName = $this->requestStack->getMasterRequest()->get('_route');
        if (false !== stripos($routeName, 'displaydeleted')) {
            return $menu;
        }
        $menu->setChildrenAttribute('class', 'nav item-actions');
    
        $this->eventDispatcher->dispatch(
            new ItemActionsMenuPreConfigurationEvent($this->factory, $menu, $options)
        );
    
        $currentUserId = $this->currentUserApi->isLoggedIn()
            ? $this->currentUserApi->get('uid')
            : UsersConstant::USER_ID_ANONYMOUS
        ;
        if ($entity instanceof PageEntity) {
            $routePrefix = 'zikulacontentmodule_page_';
            $isOwner = 0 < $currentUserId
                && null !== $entity->getCreatedBy()
                && $currentUserId === $entity->getCreatedBy()->getUid()
            ;
            
            if ('admin' === $routeArea) {
                $previewRouteParameters = $entity->createUrlArgs();
                $previewRouteParameters['preview'] = 1;
                $menu->addChild('Preview', [
                    'route' => $routePrefix . 'display',
                    'routeParameters' => $previewRouteParameters
                ])
                    ->setLinkAttribute('target', '_blank')
                    ->setLinkAttribute(
                        'title',
                        'Open preview page'
                    )
                    ->setLinkAttribute('class', 'display' === $context ? 'btn btn-sm btn-secondary' : '')
                    ->setAttribute('icon', 'fas fa-search-plus')
                ;
            }
            if ('display' !== $context) {
                $entityTitle = $this->entityDisplayHelper->getFormattedTitle($entity);
                $menu->addChild('Details', [
                    'route' => $routePrefix . $routeArea . 'display',
                    'routeParameters' => $entity->createUrlArgs()
                ])
                    ->setLinkAttribute(
                        'title',
                        str_replace('"', '', $entityTitle)
                    )
                    ->setLinkAttribute('class', 'display' === $context ? 'btn btn-sm btn-secondary' : '')
                    ->setAttribute('icon', 'fas fa-eye')
                ;
            }
            if ($this->permissionHelper->mayEdit($entity)) {
                // only allow editing for the owner or people with higher permissions
                if ($isOwner || $this->permissionHelper->hasEntityPermission($entity, ACCESS_ADD)) {
                    $menu->addChild('Edit', [
                        'route' => $routePrefix . $routeArea . 'edit',
                        'routeParameters' => $entity->createUrlArgs(true)
                    ])
                        ->setLinkAttribute(
                            'title',
                            'Edit this page'
                        )
                        ->setLinkAttribute('class', 'display' === $context ? 'btn btn-sm btn-secondary' : '')
                        ->setAttribute('icon', 'fas fa-edit')
                        ->setExtra('translation_domain', 'page')
                    ;
                    $menu->addChild('Reuse', [
                        'route' => $routePrefix . $routeArea . 'edit',
                        'routeParameters' => ['astemplate' => $entity->getKey()]
                    ])
                        ->setLinkAttribute(
                            'title',
                            'Reuse for new page'
                        )
                        ->setLinkAttribute('class', 'display' === $context ? 'btn btn-sm btn-secondary' : '')
                        ->setAttribute('icon', 'fas fa-copy')
                        ->setExtra('translation_domain', 'page')
                    ;
                    if ($this->permissionHelper->hasEntityPermission($entity, ACCESS_ADD)) {
                        $menu->addChild('Add sub page', [
                            'route' => $routePrefix . $routeArea . 'edit',
                            'routeParameters' => ['parent' => $entity->getKey()]
                        ])
                            ->setLinkAttribute(
                                'title',
                                'Add a sub page to this page'
                            )
                            ->setLinkAttribute('class', 'display' === $context ? 'btn btn-sm btn-secondary' : '')
                            ->setAttribute('icon', 'fas fa-child')
                            ->setExtra('translation_domain', 'page')
                        ;
                    }
                }
            }
            if ($this->permissionHelper->mayAccessHistory($entity)) {
                if (in_array($context, ['view', 'display']) && $this->loggableHelper->hasHistoryItems($entity)) {
                    $menu->addChild('History', [
                        'route' => $routePrefix . $routeArea . 'loggablehistory',
                        'routeParameters' => $entity->createUrlArgs()
                    ])
                        ->setLinkAttribute(
                            'title',
                            'Watch version history'
                        )
                        ->setLinkAttribute('class', 'display' === $context ? 'btn btn-sm btn-secondary' : '')
                        ->setAttribute('icon', 'fas fa-history')
                    ;
                }
            }
            if ('display' === $context) {
                $menu->addChild('Pages list', [
                    'route' => $routePrefix . $routeArea . 'view'
                ])
                    ->setLinkAttribute('class', 'display' === $context ? 'btn btn-sm btn-secondary' : '')
                    ->setAttribute('icon', 'fas fa-reply')
                    ->setExtra('translation_domain', 'page')
                ;
            }
        }
        if ($entity instanceof ContentItemEntity) {
            $routePrefix = 'zikulacontentmodule_contentitem_';
            $isOwner = 0 < $currentUserId
                && null !== $entity->getCreatedBy()
                && $currentUserId === $entity->getCreatedBy()->getUid()
            ;
        }
    
        $this->eventDispatcher->dispatch(
            new ItemActionsMenuPostConfigurationEvent($this->factory, $menu, $options)
        );
    
        return $menu;
    }
    
    /**
     * Builds the view actions menu.
     */
    public function createViewActionsMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('viewActions');
        if (!isset($options['objectType'], $options['area'])) {
            return $menu;
        }
    
        $objectType = $options['objectType'];
        $routeArea = $options['area'];
        $menu->setChildrenAttribute('class', 'nav view-actions');
    
        $this->eventDispatcher->dispatch(
            new ViewActionsMenuPreConfigurationEvent($this->factory, $menu, $options)
        );
    
        $query = $this->requestStack->getMasterRequest()->query;
        $currentTemplate = $query->getAlnum('tpl', '');
        if ('page' === $objectType) {
            $routePrefix = 'zikulacontentmodule_page_';
            $showOnlyOwn = 'admin' !== $routeArea && $this->variableApi->get('ZikulaContentModule', 'pagePrivateMode', false);
            if ('tree' === $currentTemplate) {
                if ($this->permissionHelper->hasComponentPermission($objectType, ACCESS_EDIT)) {
                    $menu->addChild('Add root node', [
                        'uri' => 'javascript:void(0)'
                    ])
                        ->setLinkAttribute('id', 'treeAddRoot')
                        ->setLinkAttribute('class', 'd-none')
                        ->setLinkAttribute('data-object-type', $objectType)
                        ->setAttribute('icon', 'fas fa-plus')
                    ;
                }
                $menu->addChild('Switch to table view', [
                    'route' => $routePrefix . $routeArea . 'view'
                ])
                    ->setAttribute('icon', 'fas fa-table')
                ;
            }
            if (!in_array($currentTemplate, ['tree'])) {
                $canBeCreated = $this->modelHelper->canBeCreated($objectType);
                if ($canBeCreated) {
                    if ($this->permissionHelper->hasComponentPermission($objectType, ACCESS_EDIT)) {
                        $menu->addChild('Create page', [
                            'route' => $routePrefix . $routeArea . 'edit'
                        ])
                            ->setAttribute('icon', 'fas fa-plus')
                            ->setExtra('translation_domain', 'page')
                        ;
                    }
                }
                $routeParameters = $query->all();
                if (1 === $query->getInt('own') && !$showOnlyOwn) {
                    $routeParameters['own'] = 1;
                } else {
                    unset($routeParameters['own']);
                }
                if (1 === $query->getInt('all')) {
                    unset($routeParameters['all']);
                    $menu->addChild('Back to paginated view', [
                        'route' => $routePrefix . $routeArea . 'view',
                        'routeParameters' => $routeParameters
                    ])
                        ->setAttribute('icon', 'fas fa-table')
                    ;
                } else {
                    $routeParameters['all'] = 1;
                    $menu->addChild('Show all entries', [
                        'route' => $routePrefix . $routeArea . 'view',
                        'routeParameters' => $routeParameters
                    ])
                        ->setAttribute('icon', 'fas fa-table')
                    ;
                }
                $menu->addChild('Switch to hierarchy view', [
                    'route' => $routePrefix . $routeArea . 'view',
                    'routeParameters' => ['tpl' => 'tree']
                ])
                    ->setAttribute('icon', 'fas fa-code-branch')
                ;
                if (!$showOnlyOwn && $this->permissionHelper->hasComponentPermission($objectType, ACCESS_EDIT)) {
                    $routeParameters = $query->all();
                    if (1 === $query->getInt('own')) {
                        unset($routeParameters['own']);
                        $menu->addChild('Show also entries from other users', [
                            'route' => $routePrefix . $routeArea . 'view',
                            'routeParameters' => $routeParameters
                        ])
                            ->setAttribute('icon', 'fas fa-users')
                        ;
                    } else {
                        $routeParameters['own'] = 1;
                        $menu->addChild('Show only own entries', [
                            'route' => $routePrefix . $routeArea . 'view',
                            'routeParameters' => $routeParameters
                        ])
                            ->setAttribute('icon', 'fas fa-user')
                        ;
                    }
                }
                // check if there exist any deleted pages
                $hasDeletedEntities = false;
                if ($this->permissionHelper->hasPermission(ACCESS_EDIT)) {
                    $hasDeletedEntities = $this->loggableHelper->hasDeletedEntities($objectType);
                }
                if ($hasDeletedEntities) {
                    $menu->addChild('View deleted pages', [
                        'route' => $routePrefix . $routeArea . 'view',
                        'routeParameters' => ['deleted' => 1]
                    ])
                        ->setAttribute('icon', 'fas fa-trash-alt')
                        ->setExtra('translation_domain', 'page')
                    ;
                }
            }
        }
    
        $this->eventDispatcher->dispatch(
            new ViewActionsMenuPostConfigurationEvent($this->factory, $menu, $options)
        );
    
        return $menu;
    }
}
