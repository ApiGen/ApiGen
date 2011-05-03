<?php

namespace Apigen;
use Apigen\Generator;
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
	 * Access level for methods.
	 *
	 * @var integer
	 */
	private static $methodAccessLevels = 0;

	/**
	 * Access level for properties.
	 *
	 * @var integer
	 */
	private static $propertyAccessLevels = 0;

	/**
	 * Inspected class reflection.
	 *
	 * @var \TokenReflection\IReflectionClass
	 */
	private $reflection;

	/**
	 * Apigen generator.
	 *
	 * @var \Apigen\Generator
	 */
	private $generator;

	/**
	 * Custom reflection data.
	 *
	 * @var array
	 */
	private $customData = array();

	/**
	 * Constructor.
	 *
	 * Sets the inspected class reflection.
	 *
	 * @param \TokenReflection\IReflectionClass $reflection Inspected class reflection
	 * @param \Apigen\Generator $generator Apigen generator
	 * @param array $customData Custom reflection data
	 */
	public function __construct(IReflectionClass $reflection, Generator $generator, array $customData = array())
	{
		if (empty(self::$methods)) {
			self::$methods = array_flip(get_class_methods($this));
		}

		if (empty(self::$methodAccessLevels)) {
			foreach ($generator->config->accessLevels as $level) {
				self::$methodAccessLevels |= constant('ReflectionMethod::IS_' . strtoupper($level));
				self::$propertyAccessLevels |= constant('ReflectionProperty::IS_' . strtoupper($level));
			}
		}

		$this->reflection = $reflection;
		$this->generator = $generator;
		$this->customData = $customData;
	}

	/**
	 * Retrieves a property or method value.
	 *
	 * First tries the envelope object's property storage, then its methods
	 * and finally the inspected class reflection.
	 *
	 * @param string $name Attribute name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->customData[$name])) {
			return $this->customData[$name];
		}

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
	 * Checks if the given property exists.
	 *
	 * First tries the envelope object's property storage, then its methods
	 * and finally the inspected class reflection.
	 *
	 * @param mixed $name Property name
	 */
	public function __isset($name)
	{
		if (isset($this->customData[$name])) {
			return true;
		}

		$key = ucfirst($name);
		return isset(self::$methods['get' . $key]) || isset(self::$methods['is' . $key]) || $this->reflection->__isset($name);
	}

	/**
	 * Stores a value to the reflection custom values storage.
	 *
	 * @param mixed $name Property name
	 * @param mixed $value Property value
	 */
	public function __set($name, $value)
	{
		$this->customData[$name] = $value;
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
		return $this->reflection->getOwnMethods(self::$methodAccessLevels);
	}

	/**
	 * Returns visible properties declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnProperties()
	{
		return $this->reflection->getOwnProperties(self::$propertyAccessLevels);
	}

	/**
	 * Returns a parent class reflection encapsulated by this class.
	 *
	 * @return \Apigen\TokenReflectionCustomClassReflection
	 */
	public function getParentClass()
	{
		$classes = $this->generator->getClasses();
		if ($class = $this->reflection->getParentClassName()) {
			return $classes[$class];
		}

		return $class;
	}

	/**
	 * Returns all parent classes reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getParentClasses()
	{
		$classes = $this->generator->getClasses();
		$generator = $this->generator;
		return array_map(function($class) use ($classes, $generator) {
			return $classes[$class->getName()];
		}, $this->reflection->getParentClasses());
	}

	/**
	 * Returns all interface reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getInterfaces()
	{
		$classes = $this->generator->getClasses();
		$generator = $this->generator;
		return array_map(function($class) use ($classes, $generator) {
			return $classes[$class->getName()];
		}, $this->reflection->getInterfaces());
	}

	/**
	 * Returns all interfaces implemented by the inspected class and not its parents.
	 *
	 * @return array
	 */
	public function getOwnInterfaces()
	{
		$classes = $this->generator->getClasses();
		$generator = $this->generator;
		return array_map(function($class) use ($classes, $generator) {
			return $classes[$class->getName()];
		}, $this->reflection->getOwnInterfaces());
	}

	/**
	 * Returns reflections of direct subclasses.
	 *
	 * @return array
	 */
	public function getDirectSubclasses()
	{
		$name = $this->name;
		return array_filter($this->generator->getClasses(), function(Reflection $class) use($name) {
			if ($class->library || !$class->isSubclassOf($name)) {
				return false;
			}

			return null === $class->getParentClassName() || !$class->getParentClass()->isSubClassOf($name);
		});
	}

	/**
	 * Returns reflections of indirect subclasses.
	 *
	 * @return array
	 */
	public function getIndirectSubclasses()
	{
		$name = $this->name;
		return array_filter($this->generator->getClasses(), function(Reflection $class) use($name) {
			if ($class->library || !$class->isSubclassOf($name)) {
				return false;
			}

			return null !== $class->getParentClassName() && $class->getParentClass()->isSubClassOf($name);
		});
	}

	/**
	 * Returns reflections of classes directly implementing this interface.
	 *
	 * @return array
	 */
	public function getDirectImplementers()
	{
		if (!$this->isInterface()) {
			return array();
		}

		$name = $this->name;
		return array_filter($this->generator->getClasses(), function(Reflection $class) use($name) {
			if ($class->library || !$class->implementsInterface($name)) {
				return false;
			}

			return null === $class->getParentClassName() || !$class->getParentClass()->implementsInterface($name);
		});
	}

	/**
	 * Returns reflections of classes indirectly implementing this interface.
	 *
	 * @return array
	 */
	public function getIndirectImplementers()
	{
		if (!$this->isInterface()) {
			return array();
		}

		$name = $this->name;
		return array_filter($this->generator->getClasses(), function(Reflection $class) use($name) {
			if ($class->library || !$class->implementsInterface($name)) {
				return false;
			}

			return null !== $class->getParentClassName() && $class->getParentClass()->implementsInterface($name);
		});
	}

	/**
	 * Returns an array of inherited methods from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedMethods()
	{
		$methods = array();
		$allMethods = $this->getOwnMethods();

		foreach ($this->getParentClasses() as $class) {
			$inheritedMethods = array();
			foreach ($class->getOwnMethods() as $name => $method) {
				if (!isset($allMethods[$name]) && !$method->isPrivate()) {
					$inheritedMethods[$name] = $method;
					$allMethods[$name] = null;
				}
			}

			if (!empty($inheritedMethods)) {
				ksort($inheritedMethods);
				$methods[$class->getName()] = array(
					'class' => $class,
					'methods' => $inheritedMethods
				);
			}
		}

		return $methods;
	}

	/**
	 * Returns an array of inherited properties from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedProperties()
	{
		$properties = array();
		$allProperties = $this->getOwnProperties();

		foreach ($this->getParentClasses() as $class) {
			$inheritedProperties = array();
			foreach ($class->getOwnProperties() as $name => $property) {
				if (!isset($allProperties[$name]) && !$property->isPrivate()) {
					$inheritedProperties[$name] = $property;
					$allProperties[$name] = null;
				}
			}

			if (!empty($inheritedProperties)) {
				ksort($inheritedProperties);
				$properties[$class->getName()] = array(
					'class' => $class,
					'properties' => $inheritedProperties
				);
			}
		}

		return $properties;
	}

	/**
	 * Returns an array of inherited constants from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedConstants()
	{
		return array_filter(
			array_map(
				function(Reflection $class) {
					$reflections = $class->getOwnConstantReflections();
					ksort($reflections);
					return empty($reflections) ? null : array(
						'class' => $class,
						'constants' => $reflections
					);
				},
				$this->getParentClasses()
			)
		);
	}
}
