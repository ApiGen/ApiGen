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

/**
 * Event.
 */
class Event extends Nette\Object
{
	/**
	 * Event name.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Event origin.
	 *
	 * @var \ApiGen\Object
	 */
	private $origin;

	/**
	 * Event payload.
	 *
	 * @var mixed
	 */
	private $payload;

	/**
	 * Is the event propagation stopped?
	 *
	 * @var boolean
	 */
	private $propagationStopped = false;

	/**
	 * Creates the event.
	 *
	 * @param \ApiGen\Origin $origin Event origin
	 * @param string $name Event name
	 * @param mixed $payload Event payload
	 */
	public function __construct(Object $origin, $name, $payload = null)
	{
		$this->origin = $origin;
		$this->name = $name;
		$this->payload = $payload;
	}

	/**
	 * Returns the event name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns event origin.
	 *
	 * @return \ApiGen\Origin
	 */
	public function getOrigin()
	{
		return $this->origin;
	}

	/**
	 * Returns the event payload.
	 *
	 * @return mixed
	 */
	public function getPayload()
	{
		return $this->payload;
	}

	/**
	 * Returns if propagation of this event was stopped.
	 *
	 * @return boolean
	 */
	public function isPropagationStopped()
	{
		return $this->propagationStopped;
	}

	/**
	 * Stops propagation of this event.
	 *
	 * @return \ApiGen\Event
	 */
	public function stopPropagation()
	{
		$this->propagationStopped = true;

		return $this;
	}
}
