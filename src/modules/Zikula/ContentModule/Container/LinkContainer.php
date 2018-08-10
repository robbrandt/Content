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

namespace Zikula\ContentModule\Container;

use Zikula\ContentModule\Container\Base\AbstractLinkContainer;
use Zikula\Core\LinkContainer\LinkContainerInterface;

/**
 * This is the link container service implementation class.
 */
class LinkContainer extends AbstractLinkContainer
{
    /**
     * @inheritDoc
     */
    public function getLinks($type = LinkContainerInterface::TYPE_ADMIN)
    {
        $links = parent::getLinks($type);
        $hasEditPermissions = $this->permissionHelper->hasComponentPermission('page', ACCESS_EDIT);
        $hasAddPermissions = $this->permissionHelper->hasComponentPermission('page', ACCESS_ADD);

        $routeArea = LinkContainerInterface::TYPE_ADMIN == $type ? 'admin' : '';

        $addNewPageLink = [
            'url' => $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'edit'),
            'text' => $this->__('Add a new page', 'zikulacontentmodule'),
            'title' => $this->__('Add a new page', 'zikulacontentmodule'),
            'icon' => 'plus-square fa-fw'
        ];

        if (LinkContainerInterface::TYPE_ACCOUNT == $type) {
            foreach ($links as $k => $v) {
                if ($v['icon'] == 'list-alt') {
                    $links[$k]['icon'] = 'book';
                }
            }
            if ($hasAddPermissions) {
                if ($this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
                    // add link between the two existing ones
                    $links = [
                        $links[0],
                        $addNewPageLink,
                        $links[1]
                    ];
                } else {
                    $links[] = $addNewPageLink;
                }
            }
        } elseif (in_array($type, [LinkContainerInterface::TYPE_ADMIN, LinkContainerInterface::TYPE_USER])) {
            $pagesSubLinks = [];
            if ($hasAddPermissions) {
                $pagesSubLinks[] = $addNewPageLink;
            }

            $pagesSubLinks[] = [
                'url' => $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'view'),
                'text' => $this->__('Tabular view', 'zikulacontentmodule'),
                'title' => $this->__('Shows the pages table', 'zikulacontentmodule'),
                'icon' => 'table fa-fw'
            ];
            if ($hasAddPermissions) {
                $pagesSubLinks[] = [
                    'url' => $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'view', ['tpl' => 'tree']),
                    'text' => $this->__('Hierarchy view', 'zikulacontentmodule'),
                    'title' => $this->__('Shows the pages tree', 'zikulacontentmodule'),
                    'icon' => 'code-fork fa-fw'
                ];
            }
            $pagesSubLinks[] = [
                'url' => $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'view', ['tpl' => 'sitemap']),
                'text' => $this->__('Sitemap', 'zikulacontentmodule'),
                'title' => $this->__('Sitemap', 'zikulacontentmodule'),
                'icon' => 'sitemap fa-fw'
            ];
            $pagesSubLinks[] = [
                'url' => $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'view', ['tpl' => 'extended']),
                'text' => $this->__('Extended', 'zikulacontentmodule'),
                'title' => $this->__('Extended page list (showing page headers)', 'zikulacontentmodule'),
                'icon' => 'list fa-fw'
            ];
            $pagesSubLinks[] = [
                'url' => $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'view', ['tpl' => 'complete']),
                'text' => $this->__('Complete', 'zikulacontentmodule'),
                'title' => $this->__('Complete page list (showing complete pages)', 'zikulacontentmodule'),
                'icon' => 'th-large fa-fw'
            ];
            $pagesSubLinks[] = [
                'url' => $this->router->generate('zikulacontentmodule_page_' . $routeArea . 'view', ['tpl' => 'categories']),
                'text' => $this->__('Category list', 'zikulacontentmodule'),
                'title' => $this->__('Show content by category', 'zikulacontentmodule'),
                'icon' => 'archive fa-fw'
            ];

            foreach ($links as $k => $v) {
                if ($v['text'] == $this->__('Pages', 'zikulacontentmodule')) {
                    $links[$k]['icon'] = 'book';
                    $links[$k]['links'] = $pagesSubLinks;
                }
            }
        }

        return $links;
    }
}
