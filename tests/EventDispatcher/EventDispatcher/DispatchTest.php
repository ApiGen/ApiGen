<?php

namespace ApiGen\EventDispatcher\Tests\EventDispatcher;

use ApiGen\Contracts\EventDispatcher\EventDispatcherInterface;
use ApiGen\EventDispatcher\Event\Event;
use ApiGen\EventDispatcher\Tests\ContainerAwareTestCase;
use ApiGen\EventDispatcher\Tests\EventDispatcher\DispatchSource\SomeService;
use ApiGen\EventDispatcher\Tests\EventDispatcher\DispatchSource\Subscriber;

class DispatchTest extends ContainerAwareTestCase
{

    /**
     * @var SomeService
     */
    private $someService;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


    protected function setUp()
    {
        $this->someService = $this->container->getByType(SomeService::class);
        $this->eventDispatcher = $this->container->getByType(EventDispatcherInterface::class);
    }


    public function testDispatch()
    {
        $this->assertSame(5, $this->someService->getValue());
        $this->eventDispatcher->dispatch(new Event(Subscriber::SOME_EVENT));
        $this->assertSame(10, $this->someService->getValue());
    }
}
