<?php

namespace CompanyOS\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('company_os_core');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('plugin')
                    ->children()
                        ->scalarNode('directories')
                            ->defaultValue('custom/plugins,custom/static-plugins')
                            ->info('Comma-separated list of plugin directories')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('auth')
                    ->children()
                        ->arrayNode('oauth2')
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultTrue()
                                    ->info('Enable OAuth2 authentication')
                                ->end()
                                ->integerNode('access_token_ttl')
                                    ->defaultValue(3600)
                                    ->info('Access token TTL in seconds')
                                ->end()
                                ->integerNode('refresh_token_ttl')
                                    ->defaultValue(1209600)
                                    ->info('Refresh token TTL in seconds')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('webhook')
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                            ->info('Enable webhook system')
                        ->end()
                        ->integerNode('max_retries')
                            ->defaultValue(3)
                            ->info('Maximum webhook retry attempts')
                        ->end()
                        ->integerNode('timeout')
                            ->defaultValue(30)
                            ->info('Webhook timeout in seconds')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
} 