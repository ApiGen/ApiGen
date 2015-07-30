<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\EventDispatcher;

use ApiGen\Contracts\EventDispatcher\Event\EventInterface;
use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\Contracts\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class SymfonyEventDispatcher implements EventDispatcherInterface
{

    /**
     * @var SymfonyEventDispatcherInterface
     */
    private $eventDispatcher;


    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * {@inheritdoc}
     */
    public function dispatch(EventInterface $event)
    {
        $listeners = $this->eventDispatcher->getListeners($event->getName());
        foreach ($listeners as $listener) {
            if (count($listener[0]) === 2) {
                $listener = $listener[0];
            }
            call_user_func($listener, $event);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function addSubscriber(EventSubscriberInterface $eventSubscriber)
    {
        foreach ($eventSubscriber->getSubscribedEvents() as $eventName => $callback) {
            $this->eventDispatcher->addListener($eventName, [$eventSubscriber, $callback]);
        }
    }
}
