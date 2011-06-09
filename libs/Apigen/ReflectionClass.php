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

use TokenReflection, TokenReflection\IReflectionClass, TokenReflection\IReflectionMethod, TokenReflection\IReflectionProperty, TokenReflection\IReflectionConstant;
use ReflectionMethod as InternalReflectionMethod, ReflectionProperty as InternalReflectionProperty;

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
	 * Access level for methods.
	 *
	 * @var integer
	 */
	private static $methodAccessLevels = false;

	/**
	 * Access level for properties.
	 *
	 * @var integer
	 */
	private static $propertyAccessLevels = false;

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
	 * Cache for list of own methods.
	 *
	 * @var array
	 */
	private $methods;

	/**
	 * Cache for list of own properties.
	 *
	 * @var array
	 */
	private $properties;

	/**
	 * Cache for list of own constants.
	 *
	 * @var array
	 */
	private $constants;

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

		if (false === self::$methodAccessLevels) {
			if (count(self::$config->accessLevels) < 3) {
				self::$methodAccessLevels = 0;
				self::$propertyAccessLevels = 0;

				foreach (self::$config->accessLevels as $level) {
					switch (strtolower($level)) {
						case 'public':
							self::$methodAccessLevels |= InternalReflectionMethod::IS_PUBLIC;
							self::$propertyAccessLevels |= InternalReflectionProperty::IS_PUBLIC;
							break;
						case 'protected':
							self::$methodAccessLevels |= InternalReflectionMethod::IS_PROTECTED;
							self::$propertyAccessLevels |= InternalReflectionProperty::IS_PROTECTED;
							break;
						case 'private':
							self::$methodAccessLevels |= InternalReflectionMethod::IS_PRIVATE;
							self::$propertyAccessLevels |= InternalReflectionProperty::IS_PRIVATE;
							break;
					}
				}
			} else {
				self::$methodAccessLevels = null;
				self::$propertyAccessLevels = null;
			}
		}
	}

	/**
	 * Returns visible methods.
	 *
	 * @return array
	 */
	public function getMethods()
	{
		if (null === $this->methods) {
			$this->methods = array();
			foreach ($this->reflection->getMethods(self::$methodAccessLevels) as $method) {
				$apiMethod = new ReflectionMethod($method, self::$generator);
				if ($apiMethod->isDocumented()) {
					$this->methods[$method->getName()] = $apiMethod;
				}
			}
		}

		return $this->methods;
	}

	/**
	 * Returns visible methods declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnMethods()
	{
		if (null === $this->ownMethods) {
			$className = $this->reflection->getName();
			$this->ownMethods = array();
			foreach ($this->getMethods() as $methodName => $method) {
				if ($className === $method->getDeclaringClassName()) {
					$this->ownMethods[$methodName] = $method;
				}
			}
		}

		return $this->ownMethods;
	}

	/**
	 * Returns a method reflection.
	 *
	 * @param string $name Method name
	 * @return \Apigen\ReflectionMethod
	 */
	public function getMethod($name)
	{
		if ($this->hasMethod($name)) {
			return $this->methods[$name];
		}

		throw new \InvalidArgumentException(sprintf('Method %s does not exist in class %s', $name, $this->reflection->getName()));
	}

	/**
	 * Returns visible properties.
	 *
	 * @return array
	 */
	public function getProperties()
	{
		if (null === $this->properties) {
			$this->properties = array();
			foreach ($this->reflection->getProperties(self::$propertyAccessLevels) as $property) {
				$apiProperty = new ReflectionProperty($property, self::$generator);
				if ($apiProperty->isDocumented()) {
					$this->properties[$property->getName()] = $apiProperty;
				}
			}
		}

		return $this->properties;
	}


	/**
	 * Returns visible properties declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnProperties()
	{
		if (null === $this->ownProperties) {
			$className = $this->reflection->getName();
			$this->ownProperties = array();
			foreach ($this->getProperties() as $propertyName => $property) {
				if ($className === $property->getDeclaringClassName()) {
					$this->ownProperties[$propertyName] = $property;
				}
			}
		}
		return $this->ownProperties;
	}

	/**
	 * Returns a method property.
	 *
	 * @param string $name Method name
	 * @return \Apigen\ReflectionProperty
	 */
	public function getProperty($name)
	{
		if ($this->hasProperty($name)) {
			return $this->properties[$name];
		}

		throw new \InvalidArgumentException(sprintf('Property %s does not exist in class %s', $name, $this->reflection->getName()));
	}

	/**
	 * Returns visible properties.
	 *
	 * @return array
	 */
	public function getConstants()
	{
		if (null === $this->constants) {
			$this->constants = array();
			foreach ($this->reflection->getConstantReflections() as $constant) {
				$apiConstant = new ReflectionConstant($constant, self::$generator);
				if ($apiConstant->isDocumented()) {
					$this->constants[$constant->getName()] = $apiConstant;
				}
			}
		}

		return $this->constants;
	}

	/**
	 * Returns constants declared by inspected class.
	 *
	 * @return array
	 */
	public function getOwnConstants()
	{
		if (null === $this->ownConstants) {
			$this->ownConstants = array();
			$className = $this->reflection->getName();
			foreach ($this->getConstants() as $constantName => $constant) {
				if ($className === $constant->getDeclaringClassName()) {
					$this->ownConstants[$constantName] = $constant;
				}
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
		if (null === $this->constants) {
			$this->getConstants();
		}

		if (isset($this->constants[$name])) {
			return $this->constants[$name];
		}

		throw new \InvalidArgumentException(sprintf('Constant %s does not exist in class %s', $name, $this->reflection->getName()));
	}

	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return \Apigen\ReflectionConstant
	 */
	public function getConstant($name)
	{
		return $this->getConstantReflection($name);
	}

	/**
	 * Checks if there is a constant of the given name.
	 *
	 * @param string $constantName Constant name
	 * @return boolean
	 */
	public function hasConstant($constantName)
	{
		if (null === $this->constants) {
			$this->getConstants();
		}

		return isset($this->constants[$constantName]);
	}

	/**
	 * Checks if there is a constant of the given name.
	 *
	 * @param string $constantName Constant name
	 * @return boolean
	 */
	public function hasOwnConstant($constantName)
	{
		if (null === $this->ownConstants) {
			$this->getOwnConstants();
		}

		return isset($this->ownConstants[$constantName]);
	}

	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return \Apigen\ReflectionConstant
	 */
	public function getOwnConstantReflection($name)
	{
		if (null === $this->ownConstants) {
			$this->getOwnConstants();
		}

		if (isset($this->ownConstants[$name])) {
			return $this->ownConstants[$name];
		}

		throw new \InvalidArgumentException(sprintf('Constant %s does not exist in class %s', $name, $this->reflection->getName()));
	}

	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return \Apigen\ReflectionConstant
	 */
	public function getOwnConstant($name)
	{
		return $this->getOwnConstantReflection($name);
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
			if (!$class->isDocumented() || !$class->isSubclassOf($name)) {
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
			if (!$class->isDocumented() || !$class->isSubclassOf($name)) {
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
			if (!$class->isDocumented() || $class->isInterface() || !$class->implementsInterface($name)) {
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
			if (!$class->isDocumented() || $class->isInterface() || !$class->implementsInterface($name)) {
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

	/**
	 * Checks if there is a property of the given name.
	 *
	 * @param string $propertyName Property name
	 * @return boolean
	 */
	public function hasProperty($propertyName)
	{
		if (null === $this->properties) {
			$this->getProperties();
		}

		return isset($this->properties[$propertyName]);
	}

	/**
	 * Checks if there is a property of the given name.
	 *
	 * @param string $propertyName Property name
	 * @return boolean
	 */
	public function hasOwnProperty($propertyName)
	{
		if (null === $this->ownProperties) {
			$this->getOwnProperties();
		}

		return isset($this->ownProperties[$propertyName]);
	}

	/**
	 * Checks if there is a method of the given name.
	 *
	 * @param string $methodName Method name
	 * @return boolean
	 */
	public function hasMethod($methodName)
	{
		if (null === $this->methods) {
			$this->getMethods();
		}

		return isset($this->methods[$methodName]);
	}

	/**
	 * Checks if there is a method of the given name.
	 *
	 * @param string $methodName Method name
	 * @return boolean
	 */
	public function hasOwnMethod($methodName)
	{
		if (null === $this->ownMethods) {
			$this->getOwnMethods();
		}

		return isset($this->ownMethods[$methodName]);
	}

	/**
	 * Returns if the class should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (null === $this->isDocumented && parent::isDocumented()) {
			foreach (self::$config->skipDocPath as $mask) {
				if (fnmatch($mask, $this->reflection->getFilename(), FNM_NOESCAPE | FNM_PATHNAME)) {
					$this->isDocumented = false;
					break;
				}
			}
			if (true === $this->isDocumented) {
				foreach (self::$config->skipDocPrefix as $prefix) {
					if (0 === strpos($this->reflection->getName(), $prefix)) {
						$this->isDocumented = false;
						break;
					}
				}
			}
		}

		return $this->isDocumented;
	}
}
