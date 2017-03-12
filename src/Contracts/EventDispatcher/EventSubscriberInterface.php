<?php

namespace ApiGen\Contracts\EventDispatcher;

interface EventSubscriberInterface
{

    /**
     * @return array {[ eventName => callback ]}
     */
    public function getSubscribedEvents();
}
