<?php

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\ContentModule\Menu;

use Knp\Menu\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zikula\ContentModule\Menu\Base\AbstractExtensionMenu;

/**
 * This is the extension menu service implementation class.
 */
class ExtensionMenu extends AbstractExtensionMenu
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function get(string $type = self::TYPE_ADMIN): ?ItemInterface
    {
        $menu = parent::get($type);
        $hasAddPermissions = $this->permissionHelper->hasComponentPermission('page', ACCESS_ADD);

        $routeArea = self::TYPE_ADMIN === $type ? 'admin' : '';

        if (self::TYPE_ACCOUNT === $type) {
            if (!$this->currentUserApi->isLoggedIn()) {
                return null;
            }
            $myPagesLabel = $this->translator->trans('My pages', [], 'page');
            $myPagesLink = $menu->getChild($myPagesLabel);
            if (null !== $myPagesLink) {
                $myPagesLink->setAttribute('icon', 'fas fa-book');
            }
            if ($hasAddPermissions) {
                $menu->addChild('Add a new page', [
                    'route' => 'zikulacontentmodule_page_' . $routeArea . 'edit'
                ])
                    ->setAttribute('icon', 'fas fa-fw fa-plus-square')
                ;

                if ($this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
                    // remove and backend link and readd it after the "add a new page" link
                    $backendLabel = $this->translator->trans('Content Backend');
                    $backendLink = $menu->getChild($backendLabel);
                    if (null !== $backendLink) {
                        $menu->removeChild($backendLabel);
                        $menu->addChild('Content Backend', [
                            'route' => 'zikulacontentmodule_page_adminindex'
                        ])
                            ->setAttribute('icon', 'fas fa-wrench')
                        ;
                    }
                }
            }

            return 0 === $menu->count() ? null : $menu;
        }

        $pagesLabel = $this->translator->trans('Pages', [], 'page');
        $pagesLink = $menu->getChild($pagesLabel);
        if (null === $pagesLink) {
            return 0 === $menu->count() ? null : $menu;
        }

        $pagesLink->setAttribute('icon', 'fas fa-book');
        if (in_array($type, [self::TYPE_ADMIN, self::TYPE_USER], true)) {
            $pagesLink->setAttribute('dropdown', true);
            $viewRouteName = 'zikulacontentmodule_page_' . $routeArea . 'view';
            if ('admin' === $routeArea) {
                if ($hasAddPermissions) {
                    $pagesLink->addChild('Add a new page', [
                        'route' => 'zikulacontentmodule_page_' . $routeArea . 'edit'
                    ])
                        ->setAttribute('icon', 'fas fa-fw fa-plus-square')
                    ;
                }
                $pagesLink->addChild('Tabular view', [
                    'route' => $viewRouteName
                ])
                    ->setAttribute('icon', 'fas fa-fw fa-table')
                    ->setLinkAttribute('title', 'Shows the pages table')
                ;
                $pagesLink->addChild('Hierarchy view', [
                    'route' => $viewRouteName,
                    'routeParameters' => ['tpl' => 'tree']
                ])
                    ->setAttribute('icon', 'fas fa-fw fa-code-branch')
                    ->setLinkAttribute('title', 'Shows the pages tree')
                ;
            } else {
                $pagesLink->addChild('Sitemap', [
                    'route' => 'zikulacontentmodule_page_sitemap'
                ])
                    ->setAttribute('icon', 'fas fa-fw fa-sitemap')
                ;
                if ($hasAddPermissions) {
                    $pagesLink->addChild('Add a new page', [
                        'route' => 'zikulacontentmodule_page_' . $routeArea . 'edit'
                    ])
                        ->setAttribute('icon', 'fas fa-fw fa-plus-square')
                    ;
                }
                $pagesLink->addChild('Simple list', [
                    'route' => $viewRouteName
                ])
                    ->setAttribute('icon', 'fas fa-fw fa-table')
                    ->setLinkAttribute('title', 'Shows a simple list of pages')
                ;
                $pagesLink->addChild('Extended list', [
                    'route' => $viewRouteName,
                    'routeParameters' => ['list' => 'extended']
                ])
                    ->setAttribute('icon', 'fas fa-fw fa-list')
                    ->setLinkAttribute('title', 'Shows an extended list of pages with first content elements')
                ;
                $pagesLink->addChild('Complete list', [
                    'route' => $viewRouteName,
                    'routeParameters' => ['list' => 'complete']
                ])
                    ->setAttribute('icon', 'fas fa-fw fa-th-large')
                    ->setLinkAttribute('title', 'Shows a complete list of pages with complete content')
                ;
                $pagesLink->addChild('Categories list', [
                    'route' => $viewRouteName,
                    'routeParameters' => ['list' => 'categories']
                ])
                    ->setAttribute('icon', 'fas fa-fw fa-archive')
                    ->setLinkAttribute('title', 'Shows content grouped by categories')
                ;
            }
        }

        return 0 === $menu->count() ? null : $menu;
    }

    /**
     * @required
     */
    public function setAdditionalDependencies(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
}
