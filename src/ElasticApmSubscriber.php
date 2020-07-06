<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizacha\ElasticApmBundle;

use PhilKra\Exception\Transaction\DuplicateTransactionNameException;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

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
    public function onKernelRequest(RequestEvent $requestEvent)
    {
        if (true === $requestEvent->isMasterRequest() && null === $this->transaction) {
            $this->transaction = $this->agentService
                ->startTransaction(
                    sprintf(
                        '%s %s (%s)',
                        $requestEvent->getRequest()->getMethod(),
                        $requestEvent->getRequest()->get('_controller'),
                        $requestEvent->getRequest()->get('_route')
                    )
                )
                ->getTransaction();
        }
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $this->agentService->error($event->getThrowable());
    }
}
