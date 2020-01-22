<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizacha\ElasticApmBundle;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use \PhilKra\Exception\Transaction\DuplicateTransactionNameException;

class ElasticApmSubscriber extends ElasticApmAbstractSubscriber
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => ['onKernelException', 100],
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    /**
     * @throws DuplicateTransactionNameException
     */
    public function onKernelRequest(GetResponseEvent $kernelEvent)
    {
        if (true === $kernelEvent->isMasterRequest() && null === $this->transaction) {
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
