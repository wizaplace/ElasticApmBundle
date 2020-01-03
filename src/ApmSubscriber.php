<?php
declare(strict_types=1);

namespace Wizacha\ApmBundle;

use PhilKra\Events\Transaction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Wizacha\ElasticApm\Service\AgentService;

class ApmSubscriber implements EventSubscriberInterface
{
    /** @var AgentService */
    private $agentService;

    private $transaction;

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

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => ['onKernelException', 100],
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    public function onKernelRequest(GetResponseEvent $kernelEvent)
    {
        if ($this->transaction === null) {
            $this->transaction = $this->agentService->startTransaction('KernelEvent')->getTransaction();
        }
    }

    public function onKernelException(GetResponseForExceptionEvent $kernelEvent)
    {
        $this->agentService->error($kernelEvent->getException());
    }

    public function onKernelTerminate()
    {
        if ($this->transaction instanceof Transaction) {
            $this->agentService->stopTransaction();
            $this->transaction = null;
        }
    }
}
