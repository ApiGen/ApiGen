<?php

namespace ApiGen\EventDispatcher\Tests\EventDispatcher\DispatchSource;

use ApiGen\Contracts\EventDispatcher\Event\EventInterface;
use ApiGen\Contracts\EventDispatcher\EventSubscriberInterface;

class Subscriber implements EventSubscriberInterface
{

    /**
     * @var string
     */
    const SOME_EVENT = 'someEvent';

    /**
     * @var SomeService
     */
    private $someService;


    public function __construct(SomeService $someService)
    {
        $this->someService = $someService;
    }


    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [self::SOME_EVENT => 'someChange'];
    }


    public function someChange(EventInterface $event)
    {
        $this->someService->setValue(10);
    }
}
