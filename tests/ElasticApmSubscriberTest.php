<?php

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Wizacha\AppBundle\EventListener\RouterListener;
use Wizacha\ElasticApmBundle\ElasticApmSubscriber;
use PHPUnit\Framework\TestCase;
use Wizacha\ElasticApm\Service\AgentService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Wizacha\ElasticApmBundle\ElasticApmSubscriberLegacy;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ElasticApmSubscriberTest extends TestCase
{
    /** @var AgentService */
    private $agentService;

    public function setUp()
    {
        parent::setUp();

        $this->agentService = $this->getMockBuilder(AgentService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->elasticApmSubscriberLegacy = $this->getMockBuilder(ElasticApmSubscriberLegacy::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testMethods()
    {
        $this->agentService
            ->expects($this->once())
            ->method('startTransaction');

        $attributes = ['_controller' => RouterListener::CSCART_CONTROLLER];
        $request = new Request(['dispatch' => 'foo.bar'], [], $attributes);
        $responseEvent = new GetResponseEvent(container()->get('http_kernel'), $request, HttpKernelInterface::MASTER_REQUEST);
        $elasticApmSubscriberLegacy = (new ElasticApmSubscriberLegacy($this->agentService));

        $elasticApmSubscriberLegacy->onKernelRequest($responseEvent);

        // Error
        $this->agentService
            ->expects($this->once())
            ->method('error');

        $exceptionEvent = new GetResponseForExceptionEvent(container()->get('http_kernel'), $request, HttpKernelInterface::MASTER_REQUEST, new Exception('argh'));
        $elasticApmSubscriberLegacy->onKernelException($exceptionEvent);
    }
}
