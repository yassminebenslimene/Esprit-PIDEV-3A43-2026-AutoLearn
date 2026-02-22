<?php
// src/Bundle/UserActivityBundle/DependencyInjection/UserActivityExtension.php

namespace App\Bundle\UserActivityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class UserActivityExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        $container->setParameter('user_activity.max_failed_attempts', $config['max_failed_attempts']);
        $container->setParameter('user_activity.lock_duration', $config['lock_duration']);
        $container->setParameter('user_activity.cleanup_after_days', $config['cleanup_after_days']);
    }
}