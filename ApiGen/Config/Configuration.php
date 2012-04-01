<?php

namespace ApiGen\Config;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Nette\DI\IContainer;
use Nette\InvalidStateException;
use RecursiveArrayIterator;

/**
 * Application configuration.
 */
class Configuration implements ArrayAccess, Countable, IteratorAggregate
{
	/**
	 * Configuration data.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Constructor.
	 *
	 * @param array $parameters Application configuration parameters
	 */
	public function __construct(array $parameters = array())
	{
		$this->fill($parameters);
	}

	/**
	 * Fills configuration from the container.
	 *
	 * @param \Nette\DI\IContainer $container Application DIC
	 * @return \ApiGen\Config\Configuration
	 */
	public function fillContainer(IContainer $container)
	{
		$this->fill($container->params);
	}

	/**
	 * Fills the configuration.
	 *
	 * @param array $parameters Configuration parameters
	 * @return \ApiGen\Config\Configuration
	 */
	protected function fill(array $parameters)
	{
		if (!empty($this->data)) {
			throw new InvalidStateException('Cannot update an already filled configuration.');
		}

		$this->data = array_map(function($value) {
			return is_array($value) ? new Configuration($value) : $value;
		}, $parameters);
	}

	/**
	 * Returns a configuration iterator.
	 *
	 * @return \RecursiveArrayIterator
	 */
	public function getIterator()
	{
		return new RecursiveArrayIterator($this->data);
	}

	/**
	 * Returns the cardinality of configuration.
	 *
	 * @return integer
	 */
	public function count()
	{
		return count($this->data);
	}

	/**
	 * Returns a configuration option value.
	 *
	 * @param string $name Option name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	/**
	 * Sets a configuration option value.
	 *
	 * @param string $name Option name
	 * @param mixed $value Option value
	 */
	public function __set($name, $value)
	{
		return $this->offsetSet($name, $value);
	}

	/**
	 * Checks if the given configuration option exists.
	 *
	 * @param string $name Option name
	 * @return boolean
	 */
	public function __isset($name)
	{
		return $this->offsetExists($name);
	}

	/**
	 * Deletes a configuration option.
	 *
	 * @param string $name Option name
	 */
	public function __unset($name)
	{
		return $this->offsetUnset($name);
	}

	/**
	 * Returns a configuration option value.
	 *
	 * @param string $name Option name
	 * @return mixed
	 */
	public function offsetGet($name)
	{
		return isset($this->data[$name]) ? $this->data[$name] : null;
	}

	/**
	 * Sets a configuration option value.
	 *
	 * @param string $name Option name
	 * @param mixed $value Option value
	 */
	public function offsetSet($name, $value)
	{
		throw new InvalidStateException('Application configuration is read-only.');
	}

	/**
	 * Checks if the given configuration option exists.
	 *
	 * @param string $name Option name
	 * @return boolean
	 */
	public function offsetExists($name)
	{
		return array_key_exists($name, $this->data);
	}

	/**
	 * Deletes a configuration option.
	 *
	 * @param string $name Option name
	 */
	public function offsetUnset($name)
	{
		throw new InvalidStateException('Application configuration is read-only.');
	}

	/**
	 * Returns the whole configuration in an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array_map(function($value) {
			return $value instanceof Configuration ? $value->toArray() : $value;
		}, $this->data);
	}
}
