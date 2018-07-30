<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\Menu\Base;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\ContentItemEntity;
use Zikula\ContentModule\Entity\SearchableEntity;

/**
 * This is the item actions menu implementation class.
 */
class AbstractItemActionsMenu implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use TranslatorTrait;

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Builds the menu.
     *
     * @param FactoryInterface $factory Menu factory
     * @param array            $options List of additional options
     *
     * @return MenuItem The assembled menu
     */
    public function menu(FactoryInterface $factory, array $options = [])
    {
        $menu = $factory->createItem('itemActions');
        if (!isset($options['entity']) || !isset($options['area']) || !isset($options['context'])) {
            return $menu;
        }

        $this->setTranslator($this->container->get('translator.default'));

        $entity = $options['entity'];
        $routeArea = $options['area'];
        $context = $options['context'];

        $permissionHelper = $this->container->get('zikula_content_module.permission_helper');
        $currentUserApi = $this->container->get('zikula_users_module.current_user');
        $entityDisplayHelper = $this->container->get('zikula_content_module.entity_display_helper');

        // return empty menu for preview of deleted items
        $request = $this->container->get('request_stack')->getMasterRequest();
        $routeName = $request->get('_route');
        if (stristr($routeName, 'displaydeleted')) {
            return $menu;
        }
        $menu->setChildrenAttribute('class', 'list-inline item-actions');

        $currentUserId = $currentUserApi->isLoggedIn() ? $currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        if ($entity instanceof PageEntity) {
            $routePrefix = 'zikulacontentmodule_page_';
            $isOwner = $currentUserId > 0 && null !== $entity->getCreatedBy() && $currentUserId == $entity->getCreatedBy()->getUid();
        
            if ($routeArea == 'admin') {
                $title = $this->__('Preview', 'zikulacontentmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . 'display',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('target', '_blank');
                $menu[$title]->setLinkAttribute('title', $this->__('Open preview page', 'zikulacontentmodule'));
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-search-plus');
            }
            if ($context != 'display') {
                $title = $this->__('Details', 'zikulacontentmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'display',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', str_replace('"', '', $entityDisplayHelper->getFormattedTitle($entity)));
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-eye');
            }
            if ($permissionHelper->mayEdit($entity)) {
                $title = $this->__('Edit', 'zikulacontentmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Edit this page', 'zikulacontentmodule'));
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-pencil-square-o');
                if (in_array($context, ['view', 'display'])) {
                    $logEntriesRepo = $this->container->get('zikula_content_module.entity_factory')->getObjectManager()->getRepository('ZikulaContentModule:PageLogEntryEntity');
                    $logEntries = $logEntriesRepo->getLogEntries($entity);
                    if (count($logEntries) > 1) {
                        $title = $this->__('History', 'zikulacontentmodule');
                        $menu->addChild($title, [
                            'route' => $routePrefix . $routeArea . 'loggablehistory',
                            'routeParameters' => $entity->createUrlArgs()
                        ]);
                        $menu[$title]->setLinkAttribute('title', $this->__('Watch version history', 'zikulacontentmodule'));
                        if ($context == 'display') {
                            $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                        }
                        $menu[$title]->setAttribute('icon', 'fa fa-history');
                    }
                }
            }
            if ($context == 'display') {
                $title = $this->__('Pages list', 'zikulacontentmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'view'
                ]);
                $menu[$title]->setLinkAttribute('title', $title);
                if ($context == 'display') {
                    $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
                }
                $menu[$title]->setAttribute('icon', 'fa fa-reply');
            }
        }
        if ($entity instanceof ContentItemEntity) {
            $routePrefix = 'zikulacontentmodule_contentitem_';
            $isOwner = $currentUserId > 0 && null !== $entity->getCreatedBy() && $currentUserId == $entity->getCreatedBy()->getUid();
        
        }
        if ($entity instanceof SearchableEntity) {
            $routePrefix = 'zikulacontentmodule_searchable_';
            $isOwner = $currentUserId > 0 && null !== $entity->getCreatedBy() && $currentUserId == $entity->getCreatedBy()->getUid();
        
        }

        return $menu;
    }
}
