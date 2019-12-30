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

namespace Zikula\ContentModule\Menu;

use Knp\Menu\ItemInterface;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Menu\Base\AbstractMenuBuilder;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;

/**
 * Menu builder implementation class.
 */
class MenuBuilder extends AbstractMenuBuilder
{
    /**
     * @var boolean
     */
    protected $multilingual;

    public function createItemActionsMenu(array $options = []): ItemInterface
    {
        $menu = parent::createItemActionsMenu($options);
        if (!isset($options['entity'], $options['area'], $options['context'])) {
            return $menu;
        }

        $entity = $options['entity'];
        if (!($entity instanceof PageEntity)) {
            return $menu;
        }

        $hasEditPermissions = $this->permissionHelper->mayEdit($entity);
        $hasContentPermissions = $this->permissionHelper->mayManagePageContent($entity);
        if (!$hasEditPermissions && !$hasContentPermissions) {
            return $menu;
        }

        $searchTitle = $this->__('Details', 'zikulacontentmodule');
        $reuseTitle = $this->__('Reuse', 'zikulacontentmodule');
        if ($hasEditPermissions) {
            $searchTitle = $reuseTitle;
        }
        $searchFound = false;
        $reappendChildren = [];
        foreach ($menu->getChildren() as $item) {
            if (!$searchFound) {
                if ($searchTitle === $item->getName()) {
                    $searchFound = true;
                    if ($searchTitle === $reuseTitle) {
                        $menu->removeChild($item);
                    }
                }
                continue;
            }
            $reappendChildren[] = $item;
            $menu->removeChild($item);
        }

        $routePrefix = 'zikulacontentmodule_page_';
        $routeArea = $options['area'];
        $context = $options['context'];

        if ($hasContentPermissions) {
            $title = $this->__('Manage content', 'zikulacontentmodule');
            $menu->addChild($title, [
                'route' => $routePrefix . $routeArea . 'managecontent',
                'routeParameters' => $entity->createUrlArgs()
            ]);
            $menu[$title]->setLinkAttribute(
                'title',
                $this->__('Manage content elements of page', 'zikulacontentmodule')
            );
            if ('display' === $context) {
                $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
            }
            $menu[$title]->setAttribute('icon', 'fa fa-cubes');
        }
        if ($hasEditPermissions) {
            $title = $this->__('Duplicate', 'zikulacontentmodule');
            $menu->addChild($title, [
                'route' => $routePrefix . $routeArea . 'duplicate',
                'routeParameters' => $entity->createUrlArgs()
            ]);
            $menu[$title]->setLinkAttribute(
                'title',
                $this->__('Duplicate this page', 'zikulacontentmodule')
            );
            if ('display' === $context) {
                $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
            }
            $menu[$title]->setAttribute('icon', 'fa fa-copy');
        }
        if ($this->multilingual && $hasEditPermissions && $hasContentPermissions) {
            $title = $this->__('Translate', 'zikulacontentmodule');
            $menu->addChild($title, [
                'route' => $routePrefix . $routeArea . 'translate',
                'routeParameters' => $entity->createUrlArgs()
            ]);
            $menu[$title]->setLinkAttribute(
                'title',
                $this->__('Translate this page', 'zikulacontentmodule')
            );
            if ('display' === $context) {
                $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
            }
            $menu[$title]->setAttribute('icon', 'fa fa-language');
        }

        foreach ($reappendChildren as $item) {
            $menu->addChild($item);
        }

        return $menu;
    }

    /**
     * @required
     */
    public function setMultilingual(VariableApiInterface $variableApi): void
    {
        $this->multilingual = $variableApi->getSystemVar('multilingual', true);
    }
}
