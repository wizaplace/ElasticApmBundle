<?php
declare(strict_types=1);

namespace Wizacha\ApmBundle;

use PhilKra\Events\Transaction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use HttpKernel\Event\ExceptionEvent;
use HttpKernel\Event\RequestEvent;
use HttpKernel\KernelEvents;
use Wizacha\ElasticApm\Service\AgentService;

abstract class ApmAbstractSubscriber implements EventSubscriberInterface
{

    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    public function __destruct()
    {
        if ($this->transaction instanceof Transaction) {
            $this->agentService->stopTransaction();
            $this->transaction = null;
        }
    }

    public function onKernelTerminate()
    {
        if ($this->transaction instanceof Transaction) {
            $this->agentService->stopTransaction();
            $this->transaction = null;
        }
    }

    abstract public static function getSubscribedEvents();
}
