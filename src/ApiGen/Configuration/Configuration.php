<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use Nette;
use Nette\Utils\ArrayHash;
use Traversable;


/**
 * @method ArrayHash getParameters()
 */
class Configuration implements \ArrayAccess, \Countable, \IteratorAggregate
{

	/**
	 * @var ArrayHash
	 */
	private $parameters;


	public function __construct(array $parameters = array())
	{
		$this->parameters = ArrayHash::from($parameters);
	}


	/**
	 * @return array
	 */
	public function toArray()
	{
		return (array) $this->parameters;
	}


	/********************* \ArrayAccess *********************/

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
	}


	/**
	 * @param mixed $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return isset($this->parameters[$offset]);
	}


	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->parameters[$offset];
	}


	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		throw new Nette\InvalidStateException('Application configuration is read-only.');
	}


	/**
	 * @param mixed $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		throw new Nette\InvalidStateException('Application configuration is read-only.');
	}


	/********************* \Countable *********************/

	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->parameters);
	}


	/********************* \IteratorAggregate *********************/

	/**
	 * @return Traversable
	 */
	public function getIterator()
	{
		return $this->parameters;
	}

}
