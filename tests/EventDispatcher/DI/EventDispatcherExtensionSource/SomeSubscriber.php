<?php

namespace ApiGen\EventDispatcher\Tests\DI\EventDispatcherExtensionSource;

use ApiGen\Contracts\EventDispatcher\EventSubscriberInterface;
use ApiGen\EventDispatcher\Event\Event;


class SomeSubscriber implements EventSubscriberInterface
{

	/**
	 * @return string[]
	 */
	public function getSubscribedEvents()
	{
		return ['eventTag' => 'someChange'];
	}


	public function someChange(Event $event)
	{
	}

}
