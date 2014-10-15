<?php

namespace Drupal\Settings;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Schema implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('drupal');
        $rootNode->children()
            ->arrayNode('settings')
                ->isRequired()
                ->children()
                    ->variableNode('databases')->isRequired()->end()
                    ->variableNode('conf')->end()
                    ->scalarNode('cookie_domain')->end()
                    ->scalarNode('db_url')->end()
                    ->scalarNode('db_prefix')->end()
                    ->scalarNode('drupal_hash_salt')
                        ->defaultValue(hash(
                            'sha256', implode('.', array(getcwd(), microtime()))
                        ))
                    ->end()
                    ->scalarNode('is_https')->end()
                    ->scalarNode('base_secure_url')->end()
                    ->scalarNode('base_insecure_url')->end()
                    ->booleanNode('update_free_access')->end()
                ->end()
            ->end()
            ->arrayNode('ini')
                ->prototype('scalar')
                    ->treatNullLike(array())
                ->end()
            ->end()
            ->arrayNode('include')
                ->addDefaultsIfNotSet()
                ->treatNullLike(array())
                ->children()
                    ->arrayNode('require')->prototype('variable')->end()->end()
                    ->arrayNode('require_once')->prototype('scalar')->end()->end()
                    ->arrayNode('include')->prototype('scalar')->end()->end()
                    ->arrayNode('include_once')->prototype('scalar')->end()->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
