<?php

namespace Jack009\PaginatorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PaginatorExtension extends Extension implements PrependExtensionInterface
{

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // set parameters so services can use them
        $container->setParameter('paginator.max_results', $config['max_results']);
        $container->setParameter('paginator.max_limit', $config['max_limit']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        // Prepend twig template path so templates from this bundle are available
        $container->prependExtensionConfig('twig', [
            'paths' => [
                __DIR__ . '/../../templates' => 'PaginatorBundle',
            ],
        ]);

        // Prepend translator path so Symfony's translator loads bundle translations automatically
        // This adds the bundle's `translations/` directory into the framework.translator.paths config
        $container->prependExtensionConfig('framework', [
            'translator' => [
                'paths' => [
                    __DIR__ . '/../../translations',
                ],
            ],
        ]);
    }

    public function getAlias(): string
    {
        return 'jack009_paginator';
    }
}