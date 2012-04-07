<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

use Nette;
use Nette\InvalidStateException;

/**
 * Base class of all ApiGen services.
 *
 * Provides event triggering functionality.
 */
abstract class Object extends Nette\Object
{
	/**
	 * Event dispatcher service.
	 *
	 * @var \ApiGen\IEventDispatcher
	 */
	private $eventDispatcher;

	/**
	 * Sets the event dispatcher.
	 *
	 * @param \ApiGen\IEventDispatcher $eventDispatcher Event dispatcher
	 * @return \ApiGen\Object
	 */
	public function setEventDispatcher(IEventDispatcher $eventDispatcher)
	{
		$this->eventDispatcher = $eventDispatcher;

		return $this;
	}

	/**
	 * Fires an event.
	 *
	 * @param string $name Event name
	 * @param mixed $payload Event payload
	 * @return \ApiGen\Object
	 * @throws \Nette\InvalidStateException If there is no event dispatcher injected
	 */
	protected function fireEvent($name, $payload = null)
	{
		if (null === $this->eventDispatcher) {
			throw new InvalidStateException(sprintf('No event dispatcher was injected into the "%s" service.', get_class($this)));
		}

		$this->eventDispatcher->dispatch($this, $name, $payload);

		return $this;
	}
}
