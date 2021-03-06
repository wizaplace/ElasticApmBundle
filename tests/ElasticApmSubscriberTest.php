<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizacha\ElasticApmBundle\Tests;

use PhilKra\Events\Transaction;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Wizacha\ElasticApm\Service\AgentService;
use Wizacha\ElasticApmBundle\ElasticApmSubscriber;

class ElasticApmSubscriberTest extends TestCase
{
    /** @var AgentService */
    private $agentService;

    /** @var KernelInterface */
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
        static::assertArrayHasKey(KernelEvents::REQUEST, ElasticApmSubscriber::getSubscribedEvents());
        static::assertArrayHasKey(KernelEvents::EXCEPTION, ElasticApmSubscriber::getSubscribedEvents());
        static::assertArrayHasKey(KernelEvents::TERMINATE, ElasticApmSubscriber::getSubscribedEvents());
    }

    public function testOnExceptionNewError(): void
    {
        $elasticApmSubscriber = new ElasticApmSubscriber($this->agentService);
        $exception = new ExceptionEvent($this->kernel, new Request(), 1, new \Exception('Ceci est une exception'));

        $this->agentService
            ->expects($this->once())
            ->method('error');

        $elasticApmSubscriber->onKernelException($exception);
    }

    public function testOnRequestNewTransaction(): void
    {
        $elasticApmSubscriber = new ElasticApmSubscriber($this->agentService);
        $event = new RequestEvent($this->kernel, new Request(), 1);

        $this->agentService
            ->expects($this->once())
            ->method('startTransaction');

        $elasticApmSubscriber->onKernelRequest($event);
    }

    public function testOnTerminateStopTransaction(): void
    {
        $this->agentService
            ->method('startTransaction')
            ->will($this->returnValue($this->agentService));
        $this->agentService
            ->method('getTransaction')
            ->will($this->returnValue(new Transaction('New transaction', [])));

        $elasticApmSubscriber = new ElasticApmSubscriber($this->agentService);
        $event = new RequestEvent($this->kernel, new Request(), 1);

        $elasticApmSubscriber->onKernelRequest($event);

        new TerminateEvent($this->kernel, new Request(), new Response());

        $this->agentService
            ->expects($this->once())
            ->method('stopTransaction');

        $elasticApmSubscriber->onKernelTerminate();
    }
}
