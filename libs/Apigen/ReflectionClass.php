<?php

/**
 * ApiGen - API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;

use TokenReflection\IReflectionClass, TokenReflection\IReflectionMethod, TokenReflection\IReflectionProperty, TokenReflection\IReflectionConstant;
use ReflectionMethod, ReflectionProperty;

/**
 * Class reflection envelope.
 *
 * Alters TokenReflection\IReflectionClass functionality for ApiGen.
 *
 * @author Jaroslav Hanslík
 * @author Ondřej Nešpor
 */
class ReflectionClass extends ReflectionBase
{
	/**
	 * List of classes.
	 *
	 * @var \ArrayObject
	 */
	private static $classes;

	/**
	 * Access level for methods.
	 *
	 * @var integer
	 */
	private static $methodAccessLevels;

	/**
	 * Access level for properties.
	 *
	 * @var integer
	 */
	private static $propertyAccessLevels;

	/**
	 * Cache for list of parent classes.
	 *
	 * @var array
	 */
	private $parentClasses;

	/**
	 * Cache for list of own methods.
	 *
	 * @var array
	 */
	private $ownMethods;

	/**
	 * Cache for list of own properties.
	 *
	 * @var array
	 */
	private $ownProperties;

	/**
	 * Cache for list of own constants.
	 *
	 * @var array
	 */
	private $ownConstants;

	/**
	 * Constructor.
	 *
	 * Sets the inspected class reflection.
	 *
	 * @param \TokenReflection\IReflectionClass $reflection Inspected class reflection
	 * @param \Apigen\Generator $generator Apigen generator
	 */
	public function __construct(IReflectionClass $reflection, Generator $generator)
	{
		parent::__construct($reflection, $generator);

		if (null === self::$classes) {
			self::$classes = $generator->getClasses();

			foreach (self::$config->accessLevels as $level) {
				self::$methodAccessLevels |= constant('ReflectionMethod::IS_' . strtoupper($level));
				self::$propertyAccessLevels |= constant('ReflectionProperty::IS_' . strtoupper($level));
			}
		}
	}

	/**
	 * Returns visible methods declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnMethods()
	{
		if (null === $this->ownMethods) {
			$this->ownMethods = $this->reflection->getOwnMethods(self::$methodAccessLevels);
			if (!self::$config->deprecated) {
				$this->ownMethods = array_filter($this->ownMethods, function(IReflectionMethod $method) {
					return !$method->isDeprecated();
				});
			}
		}

		return $this->ownMethods;
	}

	/**
	 * Returns visible properties declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnProperties()
	{
		if (null === $this->ownProperties) {
			$this->ownProperties = $this->reflection->getOwnProperties(self::$propertyAccessLevels);
			if (!self::$config->deprecated) {
				$this->ownProperties = array_filter($this->ownProperties, function(IReflectionProperty $property) {
					return !$property->isDeprecated();
				});
			}
		}
		return $this->ownProperties;
	}

	/**
	 * Returns constants declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnConstants()
	{
		if (null === $this->ownConstants) {
			$generator = self::$generator;
			$this->ownConstants = array_map(function(IReflectionConstant $constant) use ($generator) {
				return new ReflectionConstant($constant, $generator);
			}, $this->reflection->getOwnConstantReflections());
			if (!self::$config->deprecated) {
				$this->ownConstants = array_filter($this->ownConstants, function(ReflectionConstant $constant) {
					return !$constant->isDeprecated();
				});
			}
		}
		return $this->ownConstants;
	}

	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return \Apigen\ReflectionConstant
	 */
	public function getConstantReflection($name)
	{
		return new ReflectionConstant($this->reflection->getConstantReflection($name), self::$generator);
	}

	/**
	 * Returns a parent class reflection encapsulated by this class.
	 *
	 * @return \Apigen\ReflectionClass
	 */
	public function getParentClass()
	{
		if ($className = $this->reflection->getParentClassName()) {
			return self::$classes[$className];
		}
		return $className;
	}

	/**
	 * Returns all parent classes reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getParentClasses()
	{
		if (null === $this->parentClasses) {
			$classes = self::$classes;
			$this->parentClasses = array_map(function(IReflectionClass $class) use ($classes) {
				return $classes[$class->getName()];
			}, $this->reflection->getParentClasses());
		}
		return $this->parentClasses;
	}

	/**
	 * Returns all interface reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getInterfaces()
	{
		$classes = self::$classes;
		return array_map(function(IReflectionClass $class) use ($classes) {
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
		$classes = self::$classes;
		return array_map(function(IReflectionClass $class) use ($classes) {
			return $classes[$class->getName()];
		}, $this->reflection->getOwnInterfaces());
	}

	/**
	 * Returns reflections of direct subclasses.
	 *
	 * @return array
	 */
	public function getDirectSubClasses()
	{
		$subClasses = array();
		$name = $this->reflection->getName();
		foreach (self::$classes as $class) {
			if (!$class->isSubclassOf($name)) {
				continue;
			}
			if (null === $class->getParentClassName() || !$class->getParentClass()->isSubClassOf($name)) {
				$subClasses[] = $class;
			}
		}
		return $subClasses;
	}

	/**
	 * Returns reflections of indirect subclasses.
	 *
	 * @return array
	 */
	public function getIndirectSubClasses()
	{
		$subClasses = array();
		$name = $this->reflection->getName();
		foreach (self::$classes as $class) {
			if (!$class->isSubclassOf($name)) {
				continue;
			}
			if (null !== $class->getParentClassName() && $class->getParentClass()->isSubClassOf($name)) {
				$subClasses[] = $class;
			}
		}
		return $subClasses;
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

		$implementers = array();
		$name = $this->reflection->getName();
		foreach (self::$classes as $class) {
			if ($class->isInterface() || !$class->implementsInterface($name)) {
				continue;
			}
			if (null === $class->getParentClassName() || !$class->getParentClass()->implementsInterface($name)) {
				$implementers[] = $class;
			}
		}
		return $implementers;
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

		$implementers = array();
		$name = $this->reflection->getName();
		foreach (self::$classes as $class) {
			if ($class->isInterface() || !$class->implementsInterface($name)) {
				continue;
			}
			if (null !== $class->getParentClassName() && $class->getParentClass()->implementsInterface($name)) {
				$implementers[] = $class;
			}
		}
		return $implementers;
	}

	/**
	 * Returns an array of inherited methods from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedMethods()
	{
		$methods = array();
		$allMethods = array_flip(array_map(function($method) {
			return $method->getName();
		}, $this->getOwnMethods()));

		foreach ($this->getParentClasses() as $class) {
			$inheritedMethods = array();
			foreach ($class->getOwnMethods() as $method) {
				if (!isset($allMethods[$method->getName()]) && !$method->isPrivate()) {
					$inheritedMethods[$method->getName()] = $method;
					$allMethods[$method->getName()] = null;
				}
			}

			if (!empty($inheritedMethods)) {
				ksort($inheritedMethods);
				$methods[$class->getName()] = array_values($inheritedMethods);
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
		$allProperties = array_flip(array_map(function($property) {
			return $property->getName();
		}, $this->getOwnProperties()));

		foreach ($this->getParentClasses() as $class) {
			$inheritedProperties = array();
			foreach ($class->getOwnProperties() as $property) {
				if (!isset($allProperties[$property->getName()]) && !$property->isPrivate()) {
					$inheritedProperties[$property->getName()] = $property;
					$allProperties[$property->getName()] = null;
				}
			}

			if (!empty($inheritedProperties)) {
				ksort($inheritedProperties);
				$properties[$class->getName()] = array_values($inheritedProperties);
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
				function(ReflectionClass $class) {
					$reflections = $class->getOwnConstants();
					ksort($reflections);
					return $reflections;
				},
				$this->getParentClasses()
			)
		);
	}
}
