<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

namespace Wizacha\ElasticApmBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wizacha\ElasticApm\Service\AgentService;

class ElasticApmEnginePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $elasticApmEnabled = $container->resolveEnvPlaceholders($container->getParameter('elastic_apm.enabled'), true);

        // Agent parameter is set to null to prevent Philkra/Agent from communicating
        // with Elastic APM if the feature flag is set to false
        if (false === $elasticApmEnabled) {
            $container
                ->getDefinition(AgentService::class)
                ->setArgument(1, null)
            ;
        }
    }
}
