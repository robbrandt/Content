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

namespace Zikula\ContentModule\Event\Base;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

/**
 * Event base class for extending view actions menu.
 */
abstract class AbstractViewActionsMenuPostConfigurationEvent
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var ItemInterface
     */
    protected $menu;

    /**
     * @var array
     */
    protected $options;

    public function __construct(
        FactoryInterface $factory,
        ItemInterface $menu,
        array $options = []
    ) {
        $this->factory = $factory;
        $this->menu = $menu;
        $this->options = $options;
    }

    /**
     * @return FactoryInterface
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
