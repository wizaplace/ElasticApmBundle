<?php
declare(strict_types=1);

namespace Wizacha\ApmBundle;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Wizacha\ElasticApm\Service\AgentService;

class ApmSubscriber implements EventSubscriberInterface
{
    /** @var AgentService */
    private $agentService;

    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    public function __destruct()
    {
        $this->agentService->stopTransaction();
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    public function processException(KernelEvent $kernelEvent)
    {
        $this->agentService->startTransaction('coucou');
    }

    public function onKernelTerminate()
    {
        $this->agentService->stopTransaction();
    }
}
