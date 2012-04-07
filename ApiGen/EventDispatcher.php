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

use Nette\Callback;
use Nette\IFreezable;
use Nette\InvalidStateException;

/**
 * Default ApiGen event dispatcher.
 */
class EventDispatcher extends Object implements IEventDispatcher, IFreezable
{
	/**
	 * Internal event name separator.
	 *
	 * @var string
	 */
	const NAME_SEPARATOR = "\x00";

	/**
	 * List of registered origins.
	 *
	 * @var array
	 */
	private $origins = array();

	/**
	 * List of registered listeners.
	 *
	 * @var array
	 */
	private $listeners = array();

	/**
	 * Is the event dispatcher configuration frozen?
	 *
	 * @var boolean
	 */
	private $frozen = false;

	/**
	 * Dispatches an event.
	 *
	 * @param \ApiGen\Object $origin Event origin
	 * @param string $name Event name
	 * @param mixed $payload Event payload
	 * @return \ApiGen\IEventDispatcher
	 */
	public function dispatch(Object $origin, $name, $payload = null)
	{
		$event = new Event($origin, $name, $payload);

		foreach (array_keys($this->origins, strtolower(get_class($origin)), true) as $originName) {
			$internalName = $this->getInternalEventName($originName, $name);

			if (isset($this->listeners[$internalName])) {
				array_walk_recursive($this->listeners[$internalName], function(Callback $callback) use ($event) {
					if (!$event->isPropagationStopped()) {
						$callback->invoke($event);
					}
				});
			}
		}
	}

	/**
	 * Register an event origin (service).
	 *
	 * @param string $originName Origin name
	 * @param string $className Class name
	 * @return \ApiGen\IEventDispatcher
	 * @throws \Nette\InvalidStateException If the given origin is already registered
	 */
	public function registerOrigin($originName, $className)
	{
		$this->updating();

		if (isset($this->origins[$originName])) {
			throw new InvalidStateException(sprintf('Origin "%s" (%s) already registered.', $originName, $this->origins[$originName]));
		}

		$this->origins[$originName] = strtolower($className);

		return $this;
	}

	/**
	 * Register an event listener.
	 *
	 * @param string $originName Origin name
	 * @param string $eventName Event name
	 * @param \Nette\Callback $callback Registered callback
	 * @param integer $priority Listener priority
	 * @return \ApiGen\IEventDispatcher
	 * @throws \Nette\InvalidStateException If the given origin does not exist
	 */
	public function registerListener($originName, $eventName, Callback $callback, $priority = 1)
	{
		$this->updating();

		if (!isset($this->origins[$originName])) {
			throw new InvalidStateException(sprintf('Origin "%s" is not registered.', $originName));
		}

		$internalName = $this->getInternalEventName($originName, $eventName);
		$needsSorting = !isset($this->listeners[$internalName][$priority]);

		$this->listeners[$internalName][$priority][] = $callback;

		if ($needsSorting) {
			krsort($this->listeners[$internalName], SORT_NUMERIC);
		}

		return $this;
	}

	/**
	 * Freezes the event dispatcher configuration.
	 *
	 * @return \ApiGen\IEventDispatcher
	 */
	public function freeze()
	{
		$this->frozen = true;

		return $this;
	}

	/**
	 * Returns if the event dispatcher configuration is frozen.
	 *
	 * @return boolean
	 */
	public function isFrozen()
	{
		return $this->frozen;
	}

	/**
	 * Performs a check if it is possible to update the event dispatcher configuration.
	 *
	 * @throws \Nette\InvalidStateException If it is not possible to update the event dispatcher configuration
	 */
	private function updating()
	{
		if ($this->isFrozen()) {
			throw new InvalidStateException('The event dispatcher configuration is frozen.');
		}
	}

	/**
	 * Returns the internal event name.
	 *
	 * @param string $originName Origin name
	 * @param string $eventName Event name
	 * @return string
	 */
	private function getInternalEventName($originName, $eventName)
	{
		return $originName . self::NAME_SEPARATOR . $eventName;
	}
}
