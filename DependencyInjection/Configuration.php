<?php

namespace Beelab\TagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from configuration files.
 *
 * To learn more see {@link http://symfony.com/doc/current/bundles/extension.html}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('beelab_tag');
        $rootNode
            ->children()
                ->scalarNode('tag_class')
                    ->isRequired()
                ->end()
                ->booleanNode('purge')
                    ->defaultFalse()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
