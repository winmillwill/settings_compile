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
            ->variableNode('settings')
                ->treatNullLike(array())
            ->end()
            ->scalarNode('db_url')
                ->defaultValue(null)
            ->end()
            ->variableNode('ini')
                ->treatNullLike(array())
                ->defaultValue(array())
            ->end()
            ->variableNode('include')
                ->treatNullLike(array())
                ->defaultValue(array())
            ->end();
        return $treeBuilder;
    }
}
