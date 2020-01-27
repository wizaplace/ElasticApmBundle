<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizacha\ElasticApmBundle\Tests;

use PhilKra\Agent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Wizacha\ElasticApm\Service\AgentService;
use Wizacha\ElasticApmBundle\DependencyInjection\Compiler\ElasticApmEnginePass;

class ElasticApmEnginePassTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->container->getCompilerPassConfig()->setOptimizationPasses([]);
        $this->container->getCompilerPassConfig()->setRemovingPasses([]);
        $this->container->getCompilerPassConfig()->setAfterRemovingPasses([]);
        $this->container->addCompilerPass(new ElasticApmEnginePass());

        $loader = new YamlFileLoader(
            $this->container,
            new FileLocator(__DIR__ . '/../config')
        );

        $loader->load(__DIR__ . '/../config/services.yml');
    }

    protected function tearDown(): void
    {
        $this->container = null;
    }

    public function testEnabledConfiguration(): void
    {
        $this->setParameter('elastic_apm.enabled', true);
        $this->container->compile();

        static::assertSame(Agent::class, $this->container->getDefinition(Agent::class)->getClass());
        static::assertArrayHasKey(Agent::class, $this->container->getDefinitions() );

    }

    public function testDisabledConfiguration(): void
    {
        $this->setParameter('elastic_apm.enabled', false);
        $this->container->compile();

        static::assertArrayNotHasKey(Agent::class, $this->container->getDefinitions());

//        $agentService = $this->container->get(AgentService::class);
//
//        static::assertSame(null, $agentService->getAgent());
    }

    /**
     * Shortcut for quickly defining services. The returned Definition object can be further modified if necessary.
     */
    final protected function registerService(string $serviceId, string $class): Definition
    {
        $definition = new Definition($class);

        $this->container->setDefinition($serviceId, $definition);

        return $definition;
    }

    /**
     * Set a service definition you manually created.
     */
    final protected function setDefinition(string $serviceId, Definition $definition): void
    {
        $this->container->setDefinition($serviceId, $definition);
    }

    /**
     * Set a parameter.
     *
     * @param mixed $parameterValue
     */
    final protected function setParameter(string $parameterId, $parameterValue): void
    {
        $this->container->setParameter($parameterId, $parameterValue);
    }
}
