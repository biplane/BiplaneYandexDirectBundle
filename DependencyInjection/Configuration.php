<?php

namespace Biplane\Bundle\YandexDirectBundle\DependencyInjection;

use Biplane\YandexDirect\User;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @author Alexey Popkov <a.popkov@biplane.ru>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('biplane_yandex_direct');

        $rootNode
            ->children()
                ->enumNode('locale')
                    ->defaultValue(User::LOCALE_RU)
                    ->values(array(
                        User::LOCALE_RU,
                        User::LOCALE_EN,
                        User::LOCALE_UA,
                    ))
                    ->info('The locale for localize message of errors.')
                ->end()
                ->booleanNode('sandbox')
                    ->defaultFalse()
                    ->info('Enable the sandbox mode.')
                ->end()
                ->arrayNode('user')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('access_token')
                            ->isRequired()
                            ->cannotBeEmpty()
                            ->info('The access token for OAuth authorization')
                        ->end()
                        ->scalarNode('login')
                            ->info('The Yandex\'s login. Required when the master_token is set.')
                        ->end()
                        ->scalarNode('master_token')
                            ->info('The master token needs for finance operations.')
                        ->end()
                    ->end()
                    ->validate()
                        ->ifTrue(function($v) { return isset($v['master_token']) && empty($v['login']); })
                        ->thenInvalid('The login has to be specified.')
                    ->end()
                ->end()
                ->arrayNode('dump_listener')
                    ->canBeEnabled()
                    ->children()
                        ->enumNode('dump')
                            ->defaultValue('all')
                            ->values(array('all', 'only-fail'))
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('dump')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('directory')->defaultValue('%kernel.cache_dir%/api_dumps')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
