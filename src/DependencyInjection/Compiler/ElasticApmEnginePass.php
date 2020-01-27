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
        $elasticApmEnabled = $container->getParameter('elastic_apm.enabled');

        if (false === $elasticApmEnabled) {
            $container->set('PhilKra\Agent', null);
        }
    }
}
