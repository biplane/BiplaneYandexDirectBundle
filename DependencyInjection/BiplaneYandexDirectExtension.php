<?php

namespace Biplane\Bundle\YandexDirectBundle\DependencyInjection;

use Biplane\YandexDirect\EventListener\DumpListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * BiplaneYandexDirectExtension
 *
 * @author Alexey Popkov <a.popkov@biplane.ru>
 */
class BiplaneYandexDirectExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $factoryDef = $container->getDefinition('biplane_yandex_direct.factory');
        $factoryDef->addArgument($config['locale']);

        if (isset($config['user'])) {
            $factoryDef->addArgument($config['user']);
        }

        if ($config['sandbox']) {
            $factoryDef->addMethodCall('enableSandbox');
        }

        $container->getDefinition('biplane_yandex_direct.dumper')
            ->addArgument($config['dump']['directory']);

        if ($config['dump_listener']['enabled']) {
            $levelMap = array(
                'all' => DumpListener::LEVEL_ALL_REQUEST,
                'only-fail' => DumpListener::LEVEL_FAIL_REQUEST
            );

            $container->getDefinition('biplane_yandex_direct.event_listener.dump')
                ->addArgument($levelMap[$config['dump_listener']['dump']]);
        } else {
            $container->removeDefinition('biplane_yandex_direct.event_listener.dump');
        }
    }
}
