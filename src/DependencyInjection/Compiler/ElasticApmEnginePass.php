<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizacha\ElasticApmBundle\DependencyInjection\Compiler;

use PhilKra\Agent;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wizacha\ElasticApm\Service\AgentService;
use Wizacha\ElasticApmBundle\ElasticApmSubscriber;

class ElasticApmEnginePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (false === $container->resolveEnvPlaceholders($container->getParameter('elastic_apm.enabled'), true)) {
            $container->removeDefinition(AgentService::class);
            $container->removeDefinition(ElasticApmSubscriber::class);
            $container->removeDefinition(Agent::class);
        }
    }
}
