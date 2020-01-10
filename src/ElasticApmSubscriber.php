<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizacha\ElasticApmBundle;

use PhilKra\Events\Transaction;
use HttpKernel\Event\ExceptionEvent;
use HttpKernel\Event\RequestEvent;
use HttpKernel\KernelEvents;
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
}