<?php

namespace Apigen;

use TokenReflection\IReflectionClass, ReflectionMethod, ReflectionProperty;

/**
 * Class reflection envelope.
 */
class Reflection
{
	/**
	 * Class methods cache.
	 *
	 * @var array
	 */
	private static $methods = array();

	/**
	 * Inspected class reflection.
	 *
	 * @var \TokenReflection\IReflectionClass
	 */
	private $reflection;

	/**
	 * Constructor.
	 *
	 * Sets the inspected class reflection.
	 *
	 * @param \TokenReflection\IReflectionClass $reflection Inspected class reflection
	 */
	public function __construct(IReflectionClass $reflection) {
		if (empty(self::$methods)) {
			self::$methods = array_flip(get_class_methods($this));
		}

		$this->reflection = $reflection;
	}

	/**
	 * Magic __get method.
	 *
	 * First tries the envelope object's methods, than the inspected class reflection.
	 *
	 * @param string $name Attribute name
	 * @return mixed
	 */
	public function __get($name)
	{
		$key = ucfirst($name);
		if (isset(self::$methods['get' . $key])) {
			return $this->{'get' . $key}();
		} elseif (isset(self::$methods['is' . $key])) {
			return $this->{'is' . $key}();
		} else {
			return $this->reflection->__get($name);
		}
	}

	/**
	 * Calls a method of the inspected class reflection.
	 *
	 * @param string $name Method name
	 * @param array $args Arguments
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		return call_user_func_array(array($this->reflection, $name), $args);
	}

	/**
	 * Returns visible methods declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnMethods()
	{
		return $this->reflection->getOwnMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED);
	}

	/**
	 * Returns visible properties declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnProperties()
	{
		return $this->reflection->getOwnProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
	}

	/**
	 * Returns a parent class reflection encapsulated by this class.
	 *
	 * @return \Apigen\TokenReflectionCustomClassReflection
	 */
	public function getParentClass()
	{
		$class = $this->reflection->getParentClass();
		return $class ? new self($class) : $class;
	}

	/**
	 * Returns all parent classes reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getParentClasses()
	{
		return array_map(function($class) {
			return new Reflection($class);
		}, $this->reflection->getParentClasses());
	}

	/**
	 * Returns all interface reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getInterfaces()
	{
		return array_map(function($class) {
			return new Reflection($class);
		}, $this->reflection->getInterfaces());
	}

	/**
	 * Returns all interfaces implemented by the inspected class and not its parents.
	 *
	 * @return array
	 */
	public function getOwnInterfaces()
	{
		return array_map(function($class) {
			return new Reflection($class);
		}, $this->reflection->getOwnInterfaces(false));
	}
}
