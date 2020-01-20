<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizacha\ElasticApmBundle\tests;

use PHPUnit\Framework\TestCase;
use PhilKra\Events\Transaction;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Wizacha\ElasticApm\Service\AgentService;
use Wizacha\ElasticApmBundle\ElasticApmSubscriberLegacy;

class ElasticApmSubscriberLegacyTest extends TestCase
{
    /** @var AgentService */
    private $agentService;

    /** @var KernelInterface  */
    private $kernel;

    public function setUp(): void
    {
        parent::setUp();

        $this->agentService = $this->getMockBuilder(AgentService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->kernel = $this->getMockBuilder(KernelInterface::class)
            ->getMock();
    }


    public function testEventSubscription(): void
    {
        static::assertArrayHasKey(KernelEvents::REQUEST, ElasticApmSubscriberLegacy::getSubscribedEvents());
        static::assertArrayHasKey(KernelEvents::EXCEPTION, ElasticApmSubscriberLegacy::getSubscribedEvents());
        static::assertArrayHasKey(KernelEvents::TERMINATE, ElasticApmSubscriberLegacy::getSubscribedEvents());
    }

    public function testOnExceptionNewError(): void
    {
        $elasticApmSubscriberLegacy = new ElasticApmSubscriberLegacy($this->agentService);
        $exception = new GetResponseForExceptionEvent($this->kernel, new Request(), 1, new \Exception('Ceci est une exception'));

        $this->agentService
            ->expects($this->once())
            ->method('error');

        $elasticApmSubscriberLegacy->onKernelException($exception);
    }

    public function testOnRequestNewTransaction(): void
    {
        $elasticApmSubscriberLegacy = new ElasticApmSubscriberLegacy($this->agentService);
        $event = new GetResponseEvent($this->kernel, new Request(), 1);

        $this->agentService
            ->expects($this->once())
            ->method('startTransaction');

        $elasticApmSubscriberLegacy->onKernelRequest($event);
    }

    public function testOnTerminateStopTransaction(): void
    {
        $this->agentService
            ->method('startTransaction')
            ->will($this->returnValue($this->agentService));
        $this->agentService
            ->method('getTransaction')
            ->will($this->returnValue(new Transaction('New transaction', [])));

        $elasticApmSubscriberLegacy = new ElasticApmSubscriberLegacy($this->agentService);
        $event = new GetResponseEvent($this->kernel, new Request(), 1);

        $elasticApmSubscriberLegacy->onKernelRequest($event);

        new PostResponseEvent($this->kernel, new Request(), new Response());

        $this->agentService
            ->expects($this->once())
            ->method('stopTransaction');

        $elasticApmSubscriberLegacy->onKernelTerminate();
    }
}
