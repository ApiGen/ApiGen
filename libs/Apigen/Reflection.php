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
use Apigen\Generator;
use TokenReflection\IReflectionClass, ReflectionMethod, ReflectionProperty;

/**
 * Class reflection envelope.
 *
 * Alters TokenReflection\IReflectionClass functionality for ApiGen.
 *
 * @author Jaroslav Hanslík
 * @author Ondřej Nešpor
 */
class Reflection
{
	/**
	 * Config.
	 *
	 * @var \Apigen\Config
	 */
	private static $config = null;

	/**
	 * List of classes.
	 *
	 * @var \ArrayObject
	 */
	private static $classes = array();

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
	 * Cache for information if the class should be documented.
	 *
	 * @var boolean
	 */
	private $isDocumented;

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
		if (null === self::$config) {
			self::$config = $generator->getConfig();
			self::$classes = $generator->getClasses();

			self::$methods = array_flip(get_class_methods($this));

			foreach (self::$config->accessLevels as $level) {
				self::$methodAccessLevels |= constant('ReflectionMethod::IS_' . strtoupper($level));
				self::$propertyAccessLevels |= constant('ReflectionProperty::IS_' . strtoupper($level));
			}
		}

		$this->reflection = $reflection;
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
	 * @return boolean
	 */
	public function __isset($name)
	{
		$key = ucfirst($name);
		return isset(self::$methods['get' . $key]) || isset(self::$methods['is' . $key]) || $this->reflection->__isset($name);
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
		if (null === $this->ownMethods) {
			$this->ownMethods = $this->reflection->getOwnMethods(self::$methodAccessLevels);
			if (!self::$config->deprecated) {
				$this->ownMethods = array_filter($this->ownMethods, function($method) {
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
				$this->ownProperties = array_filter($this->ownProperties, function($property) {
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
			$this->ownConstants = $this->reflection->getOwnConstantReflections();
			if (!self::$config->deprecated) {
				$this->ownConstants = array_filter($this->ownConstants, function($constant) {
					return !$constant->isDeprecated();
				});
			}
		}
		return $this->ownConstants;
	}

	/**
	 * Returns a parent class reflection encapsulated by this class.
	 *
	 * @return \Apigen\Reflection
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
			$this->parentClasses = array_map(function($class) use ($classes) {
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
		return array_map(function($class) use ($classes) {
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
		return array_map(function($class) use ($classes) {
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
				$methods[$class->getName()] = $inheritedMethods;
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
				$properties[$class->getName()] = $inheritedProperties;
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
					return $reflections;
				},
				$this->getParentClasses()
			)
		);
	}

	/**
	 * Returns if the class should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (null === $this->isDocumented) {
			if (self::$config->php && $this->reflection->isInternal()) {
				$this->isDocumented = true;
			} elseif (!$this->reflection->isTokenized()) {
				$this->isDocumented = false;
			} elseif (!self::$config->deprecated && $this->reflection->isDeprecated()) {
				$this->isDocumented = false;
			} else {
				$this->isDocumented = true;
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
		}
		return $this->isDocumented;
	}
}
