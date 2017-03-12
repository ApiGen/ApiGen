<?php

namespace ApiGen\Contracts\EventDispatcher;

use ApiGen\Contracts\EventDispatcher\Event\EventInterface;

interface EventDispatcherInterface
{

    /**
     * Dispatches an event to all registered listeners.
     */
    public function dispatch(EventInterface $event);


    /**
     * Adds an event subscriber.
     */
    public function addSubscriber(EventSubscriberInterface $eventSubscriber);
}
