<?php
// src/Bundle/UserActivityBundle/DependencyInjection/Configuration.php

namespace App\Bundle\UserActivityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('user_activity');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->integerNode('max_failed_attempts')->defaultValue(5)->end()
                ->integerNode('lock_duration')->defaultValue(10)->end()
                ->integerNode('cleanup_after_days')->defaultValue(90)->end()
                ->arrayNode('tracked_actions')
                    ->scalarPrototype()->end()
                    ->defaultValue(['LOGIN_SUCCESS', 'LOGIN_FAILED', 'LOGOUT'])
                ->end()
            ->end();

        return $treeBuilder;
    }
}