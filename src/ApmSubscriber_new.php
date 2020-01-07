<?php
declare(strict_types=1);

namespace Wizacha\ApmBundle;

use PhilKra\Events\Transaction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use HttpKernel\Event\ExceptionEvent;
use HttpKernel\Event\RequestEvent;
use HttpKernel\KernelEvents;
use Wizacha\ElasticApm\Service\AgentService;

class ApmSubscriberNew implements EventSubscriberInterface
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

    public function onKernelRequest(RequestEvent $kernelEvent)
    {
        if (true === $kernelEvent->isMasterRequest() && $this->transaction === null) {
            $this->transaction = $this->agentService
                ->startTransaction(
                    sprintf(
                        '%s %s (%s)',
                        $kernelEvent->getRequest()->getMethod(),
                        $kernelEvent->getRequest()->get('_controller'),
                        $kernelEvent->getRequest()->get('_route')
                    )
                )
                ->getTransaction();
        }
    }

    public function onKernelException(ExceptionEvent $kernelEvent)
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
