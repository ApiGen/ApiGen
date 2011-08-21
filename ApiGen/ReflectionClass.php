<?php

/**
 * ApiGen 2.0.3 - API documentation generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

use TokenReflection, TokenReflection\IReflectionClass, TokenReflection\IReflectionMethod, TokenReflection\IReflectionProperty, TokenReflection\IReflectionConstant;
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
	 * @param \ApiGen\Generator $generator ApiGen generator
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

			if ((ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE) === self::$methodAccessLevels) {
				self::$methodAccessLevels = null;
			}
			if ((ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE) === self::$propertyAccessLevels) {
				self::$propertyAccessLevels = null;
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
			if (!self::$config->internal) {
				$this->ownMethods = array_filter($this->ownMethods, function(IReflectionMethod $method) {
					return (!$internal = $method->getAnnotation('internal')) || !empty($internal[0]);
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
			if (!self::$config->internal) {
				$this->ownProperties = array_filter($this->ownProperties, function(IReflectionProperty $property) {
					return (!$internal = $property->getAnnotation('internal')) || !empty($internal[0]);
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
			$this->ownConstants = $this->reflection->getOwnConstantReflections();

			if (!self::$config->deprecated) {
				$this->ownConstants = array_filter($this->ownConstants, function(IReflectionConstant $constant) {
					return !$constant->isDeprecated();
				});
			}
			if (!self::$config->internal) {
				$this->ownConstants = array_filter($this->ownConstants, function(IReflectionConstant $constant) {
					return (!$internal = $constant->getAnnotation('internal')) || !empty($internal[0]);
				});
			}

			$generator = self::$generator;
			$this->ownConstants = array_map(function(IReflectionConstant $constant) use ($generator) {
				return new ReflectionConstant($constant, $generator);
			}, $this->ownConstants);
		}
		return $this->ownConstants;
	}

	/**
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return \ApiGen\ReflectionConstant
	 */
	public function getConstantReflection($name)
	{
		return new ReflectionConstant($this->reflection->getConstantReflection($name), self::$generator);
	}

	/**
	 * Returns a parent class reflection encapsulated by this class.
	 *
	 * @return \ApiGen\ReflectionClass
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
		}, array_reverse($this->reflection->getInterfaces()));
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

		foreach (array_merge($this->getParentClasses(), $this->getInterfaces()) as $class) {
			$inheritedMethods = array();
			foreach ($class->getOwnMethods() as $method) {
				if (!array_key_exists($method->getName(), $allMethods) && !$method->isPrivate()) {
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

		foreach (array_merge($this->getParentClasses(), $this->getInterfaces()) as $class) {
			$inheritedProperties = array();
			foreach ($class->getOwnProperties() as $property) {
				if (!array_key_exists($property->getName(), $allProperties) && !$property->isPrivate()) {
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
				array_merge($this->getParentClasses(), $this->getInterfaces())
			)
		);
	}

	/**
	 * Checks if there is a constant of the given name.
	 *
	 * @param string $constantName Constant name
	 * @return boolean
	 */
	public function hasConstant($constantName)
	{
		try {
			$constant = $this->reflection->getConstantReflection($constantName);

			if (!self::$config->deprecated && $constant->isDeprecated()) {
				// Deprecated constant
				return false;
			}

			if (!self::$config->internal && ($internal = $constant->getAnnotation('internal')) && empty($internal[0])) {
				// Internal constant
				return false;
			}

			return self::$classes[$constant->getDeclaringClassName()]->isDocumented();
		} catch (TokenReflection\Exception $e) {
			return false;
		}
	}

	/**
	 * Checks if there is a property of the given name.
	 *
	 * @param string $propertyName Property name
	 * @return boolean
	 */
	public function hasProperty($propertyName)
	{
		try {
			$property = $this->reflection->getProperty($propertyName);

			if (!self::$config->deprecated && $property->isDeprecated()) {
				// Deprecated property
				return false;
			}

			if (!self::$config->internal && ($internal = $property->getAnnotation('internal')) && empty($internal[0])) {
				// Internal property
				return false;
			}

			if (null !== self::$propertyAccessLevels) {
				if (!($property->getModifiers() & self::$propertyAccessLevels)) {
					// Wrong access level
					return false;
				}
			}

			return self::$classes[$property->getDeclaringClassName()]->isDocumented();
		} catch (TokenReflection\Exception $e) {
			return false;
		}
	}

	/**
	 * Checks if there is a method of the given name.
	 *
	 * @param string $methodName Method name
	 * @return boolean
	 */
	public function hasMethod($methodName)
	{
		try {
			$method = $this->reflection->getMethod($methodName);

			if (!self::$config->deprecated && $method->isDeprecated()) {
				// Deprecated method
				return false;
			}

			if (!self::$config->internal && ($internal = $method->getAnnotation('internal')) && empty($internal[0])) {
				// Internal method
				return false;
			}

			if (null !== self::$methodAccessLevels) {
				if (!$method->is(self::$methodAccessLevels)) {
					// Wrong access level
					return false;
				}
			}

			return self::$classes[$method->getDeclaringClassName()]->isDocumented();
		} catch (TokenReflection\Exception $e) {
			return false;
		}
	}
}
