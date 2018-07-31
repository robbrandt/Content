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

namespace Zikula\ContentModule\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ContentTypeCollectorPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('zikula_content_module.collector.content_type_collector')) {
            return;
        }

        $collectorDefinition = $container->getDefinition('zikula_content_module.collector.content_type_collector');

        $taggedServices = $container->findTaggedServiceIds('zikula.content_type');
        foreach ($taggedServices as $id => $tagParameters) {
            $collectorDefinition->addMethodCall('add', [new Reference($id)]);
        }
    }
}
