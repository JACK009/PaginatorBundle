<?php

namespace Jack009\PaginatorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PaginatorExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container): void
    {
        if ($container->hasDefinition('twig.loader.filesystem')) {
            $container
                ->getDefinition('twig.loader.filesystem')
                ->addMethodCall(
                    'addPath',
                    [__DIR__ . '/../../templates', 'PaginatorBundle']
                );
        }

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../config')
        );
        $loader->load('services.yaml');
    }
}