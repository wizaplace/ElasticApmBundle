<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizacha\ElasticApmBundle;

use PhilKra\Events\Transaction;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Wizacha\ElasticApm\Service\AgentService;

class ElasticApmSubscriber extends ElasticApmAbstractSubscriber
{
    /** @var AgentService */
    protected $agentService;

    /** @var Transaction */
    protected $transaction;

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

    public function onKernelException(GetResponseForExceptionEvent $kernelEvent)
    {
        $this->agentService->error($kernelEvent->getException());
    }
}
