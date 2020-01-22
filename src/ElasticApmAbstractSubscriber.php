<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizacha\ElasticApmBundle;

use PhilKra\Events\Transaction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Wizacha\ElasticApm\Service\AgentService;

abstract class ElasticApmAbstractSubscriber implements EventSubscriberInterface
{
    /** @var \Wizacha\ElasticApm\Service\AgentService */
    protected $agentService;

    /** @var \PhilKra\Events\Transaction|null */
    protected $transaction;

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
