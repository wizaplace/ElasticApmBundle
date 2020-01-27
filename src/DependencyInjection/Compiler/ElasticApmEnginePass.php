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
        $elasticApmEnabled = $container->resolveEnvPlaceholders($container->getParameter('elastic_apm.enabled'), true);

        if (false === $elasticApmEnabled) {
            $container->getDefinition('Wizacha\ElasticApm\Service\AgentService')
                ->setArgument(1, null);
        }
    }
}
