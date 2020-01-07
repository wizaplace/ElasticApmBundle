<?php

namespace Wizacha\ApmBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class ApmBundleExtension extends Extension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../config')
        );

        if (Kernel::VERSION_ID >= 4300) {
            $loader->load(__DIR__.'/../config/services_new.yml');
        } else {
            $loader->load(__DIR__.'/../config/services.yml');
        }
    }
}
