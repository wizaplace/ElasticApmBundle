<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizacha\ElasticApmBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ElasticApmEnginePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (false === $container->resolveEnvPlaceholders($container->getParameter('elastic_apm.enabled'), true)) {
            $container->removeDefinition('Wizacha\ElasticApm\Service\AgentService');
            $container->removeDefinition('Wizacha\ElasticApmBundle\ElasticApmSubscriber');
            $container->removeDefinition('PhilKra\Agent');
        }
    }
}
