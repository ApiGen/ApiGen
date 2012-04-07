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

/**
 * Event dispatcher service interface.
 */
interface IEventDispatcher
{
	/**
	 * Dispatches an event.
	 *
	 * @param \ApiGen\Object $origin Event origin
	 * @param string $name Event name
	 * @param mixed $payload Event payload
	 * @return \ApiGen\IEventDispatcher
	 */
	public function dispatch(Object $origin, $name, $payload = null);

	/**
	 * Register an event origin (service).
	 *
	 * @param string $originName Origin name
	 * @param string $className Class name
	 * @return \ApiGen\IEventDispatcher
	 */
	public function registerOrigin($originName, $className);

	/**
	 * Register an event listener.
	 *
	 * @param string $originName Origin name
	 * @param string $eventName Event name
	 * @param \Nette\Callback $callback Registered callback
	 * @param integer $priority Listener priority
	 * @return \ApiGen\IEventDispatcher
	 */
	public function registerListener($originName, $eventName, Callback $callback, $priority = 1);
}
