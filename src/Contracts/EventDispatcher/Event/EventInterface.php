<?php

namespace ApiGen\Contracts\EventDispatcher\Event;

interface EventInterface
{

    /**
     * @return string
     */
    public function getName();
}
