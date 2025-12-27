<?php

namespace Jack009\PaginatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jack009_paginator');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->integerNode('max_results')->defaultValue(10)->min(1)->end()
                ->integerNode('max_limit')->defaultValue(100)->min(1)->max(100)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

