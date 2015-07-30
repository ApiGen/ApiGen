<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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
