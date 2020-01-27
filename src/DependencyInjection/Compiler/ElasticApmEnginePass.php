<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizacha\ElasticApmBundle\DependencyInjection\Compiler;

use PhilKra\Agent;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;


class ElasticApmEnginePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $elasticApmEnabled = $container->resolveEnvPlaceholders($container->getParameter('elastic_apm.enabled'));

        if (false === $elasticApmEnabled) {
            $container->getDefinition('Wizacha\ElasticApm\Service\AgentService')
                ->setArgument(1, null);
        }
    }
}
