<?php

namespace Beelab\TagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('beelab_tag');
        $rootNode = $treeBuilder->getRootNode();
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
