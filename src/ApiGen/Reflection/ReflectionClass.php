<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\Parser\ParserResult;
use ArrayObject;
use InvalidArgumentException;
use Nette\Utils\ArrayHash;
use ReflectionMethod as InternalReflectionMethod;
use ReflectionProperty as InternalReflectionProperty;
use TokenReflection;
use TokenReflection\IReflectionClass;


class ReflectionClass extends ReflectionElement
{

	/**
	 * @var integer
	 */
	private static $methodAccessLevels = 0;

	/**
	 * @var integer
	 */
	private static $propertyAccessLevels = 0;

	/**
	 * @var array
	 */
	private $parentClasses;

	/**
	 * @var array
	 */
	private $ownMethods;

	/**
	 * @var array
	 */
	private $ownMagicMethods;

	/**
	 * @var array
	 */
	private $ownProperties;

	/**
	 * @var array
	 */
	private $ownMagicProperties;

	/**
	 * @var array
	 */
	private $ownConstants;

	/**
	 * @var array
	 */
	private $methods;

	/**
	 * @var array
	 */
	private $properties;

	/**
	 * @var array
	 */
	private $constants;


	public function __construct(IReflectionClass $reflection)
	{
		parent::__construct($reflection);
		self::$propertyAccessLevels = Configuration::$config->propertyAccessLevels;
		self::$methodAccessLevels = Configuration::$config->methodAccessLevels;
	}


	/**
	 * Returns FQN name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->reflection->getName();
	}


	/**
	 * Returns the unqualified name (UQN).
	 *
	 * @return string
	 */
	public function getShortName()
	{
		return $this->reflection->getShortName();
	}


	/**
	 * @return array
	 */
	public function getModifiers()
	{
		return $this->reflection->getModifiers();
	}


	/**
	 * @return bool
	 */
	public function isAbstract()
	{
		return $this->reflection->isAbstract();
	}


	/**
	 * @return bool
	 */
	public function isFinal()
	{
		return $this->reflection->isFinal();
	}


	/**
	 * @return bool
	 */
	public function isInterface()
	{
		return $this->reflection->isInterface();
	}


	/**
	 * @return bool
	 */
	public function isException()
	{
		return $this->reflection->isException();
	}


	/**
	 * @param string $class
	 * @return bool
	 */
	public function isSubclassOf($class)
	{
		return $this->reflection->isSubclassOf($class);
	}


	/**
	 * @return ReflectionMethod[]|array
	 */
	public function getMethods()
	{
		if ($this->methods === NULL) {
			$this->methods = $this->getOwnMethods();

			foreach ($this->reflection->getMethods(self::$methodAccessLevels) as $method) {
				/** @var ReflectionElement|TokenReflection\Php\IReflection $method */
				if (isset($this->methods[$method->getName()])) {
					continue;
				}
				$apiMethod = new ReflectionMethod($method);
				if ( ! $this->isDocumented() || $apiMethod->isDocumented()) {
					$this->methods[$method->getName()] = $apiMethod;
				}
			}
		}
		return $this->methods;
	}


	/**
	 * @return ReflectionMethod[]|array
	 */
	public function getOwnMethods()
	{
		if ($this->ownMethods === NULL) {
			$this->ownMethods = [];

			foreach ($this->reflection->getOwnMethods(self::$methodAccessLevels) as $method) {
				$apiMethod = new ReflectionMethod($method);
				if ( ! $this->isDocumented() || $apiMethod->isDocumented()) {
					$this->ownMethods[$method->getName()] = $apiMethod;
				}
			}
		}
		return $this->ownMethods;
	}


	/**
	 * @return ReflectionMethod[]|array
	 */
	public function getMagicMethods()
	{
		$methods = $this->getOwnMagicMethods();

		$parent = $this->getParentClass();
		while ($parent) {
			foreach ($parent->getOwnMagicMethods() as $method) {
				if (isset($methods[$method->getName()])) {
					continue;
				}

				if ( ! $this->isDocumented() || $method->isDocumented()) {
					$methods[$method->getName()] = $method;
				}
			}
			$parent = $parent->getParentClass();
		}

		foreach ($this->getTraits() as $trait) {
			if ( ! $trait instanceof ReflectionClass) {
				continue;
			}

			foreach ($trait->getOwnMagicMethods() as $method) {
				if (isset($methods[$method->getName()])) {
					continue;
				}

				if ( ! $this->isDocumented() || $method->isDocumented()) {
					$methods[$method->getName()] = $method;
				}
			}
		}

		return $methods;
	}


	/**
	 * @return ReflectionMethod[]|array
	 */
	public function getOwnMagicMethods()
	{
		if ($this->ownMagicMethods === NULL) {
			$this->ownMagicMethods = [];

			if ( ! (self::$methodAccessLevels & InternalReflectionMethod::IS_PUBLIC)
				|| $this->getDocComment() === FALSE
			) {
				return $this->ownMagicMethods;
			}

			$annotations = $this->getAnnotation('method');
			if ($annotations === NULL) {
				return $this->ownMagicMethods;
			}

			foreach ($annotations as $annotation) {
				$this->processAnnotation($annotation);
			}
		}
		return $this->ownMagicMethods;
	}


	/**
	 * @return array
	 */
	public function getTraitMethods()
	{
		$methods = [];
		foreach ($this->reflection->getTraitMethods(self::$methodAccessLevels) as $method) {
			$apiMethod = new ReflectionMethod($method);
			if ( ! $this->isDocumented() || $apiMethod->isDocumented()) {
				/** @var ReflectionElement $method */
				$methods[$method->getName()] = $apiMethod;
			}
		}
		return $methods;
	}


	/**
	 * @param string $name Method name
	 * @return ReflectionMethod
	 * @throws \InvalidArgumentException If required method does not exist.
	 */
	public function getMethod($name)
	{
		if ($this->hasMethod($name)) {
			return $this->methods[$name];
		}

		throw new InvalidArgumentException(sprintf(
			'Method %s does not exist in class %s', $name, $this->reflection->getName()
		));
	}


	/**
	 * @return array
	 */
	public function getProperties()
	{
		if ($this->properties === NULL) {
			$this->properties = $this->getOwnProperties();
			foreach ($this->reflection->getProperties(self::$propertyAccessLevels) as $property) {
				/** @var ReflectionElement $property */
				if (isset($this->properties[$property->getName()])) {
					continue;
				}
				$apiProperty = new ReflectionProperty($property);
				if ( ! $this->isDocumented() || $apiProperty->isDocumented()) {
					$this->properties[$property->getName()] = $apiProperty;
				}
			}
		}
		return $this->properties;
	}


	/**
	 * @return array
	 */
	public function getMagicProperties()
	{
		$properties = $this->getOwnMagicProperties();

		$parent = $this->getParentClass();
		while ($parent) {
			foreach ($parent->getOwnMagicProperties() as $property) {
				if (isset($properties[$property->getName()])) {
					continue;
				}
				if ( ! $this->isDocumented() || $property->isDocumented()) {
					$properties[$property->getName()] = $property;
				}
			}
			$parent = $parent->getParentClass();
		}

		foreach ($this->getTraits() as $trait) {
			if ( ! $trait instanceof ReflectionClass) {
				continue;
			}

			foreach ($trait->getOwnMagicProperties() as $property) {
				if (isset($properties[$property->getName()])) {
					continue;
				}

				if ( ! $this->isDocumented() || $property->isDocumented()) {
					$properties[$property->getName()] = $property;
				}
			}
		}

		return $properties;
	}


	/**
	 * @return ReflectionProperty[]|array
	 */
	public function getOwnProperties()
	{
		if ($this->ownProperties === NULL) {
			$this->ownProperties = [];
			foreach ($this->reflection->getOwnProperties(self::$propertyAccessLevels) as $property) {
				$apiProperty = new ReflectionProperty($property);
				if ( ! $this->isDocumented() || $apiProperty->isDocumented()) {
					/** @var ReflectionElement $property */
					$this->ownProperties[$property->getName()] = $apiProperty;
				}
			}
		}
		return $this->ownProperties;
	}


	/**
	 * @return ReflectionProperty[]|array
	 */
	public function getOwnMagicProperties()
	{
		if ($this->ownMagicProperties === NULL) {
			$this->ownMagicProperties = [];

			if ( ! (self::$propertyAccessLevels & InternalReflectionProperty::IS_PUBLIC) || $this->getDocComment() === FALSE) {
				return $this->ownMagicProperties;
			}

			foreach (['property', 'property-read', 'property-write'] as $annotationName) {
				$annotations = $this->getAnnotation($annotationName);
				if ($annotations === NULL) {
					continue;
				}

				foreach ($annotations as $annotation) {
					$this->processMagicPropertyAnnotation($annotation, $annotationName);
				}
			}
		}

		return $this->ownMagicProperties;
	}


	/**
	 * @return array
	 */
	public function getTraitProperties()
	{
		$properties = [];
		foreach ($this->reflection->getTraitProperties(self::$propertyAccessLevels) as $property) {
			$apiProperty = new ReflectionProperty($property);
			if ( ! $this->isDocumented() || $apiProperty->isDocumented()) {
				/** @var ReflectionElement $property */
				$properties[$property->getName()] = $apiProperty;
			}
		}
		return $properties;
	}


	/**
	 * @param string $name Method name
	 * @return ReflectionProperty
	 * @throws \InvalidArgumentException If required property does not exist.
	 */
	public function getProperty($name)
	{
		if ($this->hasProperty($name)) {
			return $this->properties[$name];
		}

		throw new InvalidArgumentException(sprintf(
			'Property %s does not exist in class %s', $name, $this->reflection->getName()
		));
	}


	/**
	 * @return ReflectionConstant[]|array
	 */
	public function getConstants()
	{
		if ($this->constants === NULL) {
			$this->constants = [];
			foreach ($this->reflection->getConstantReflections() as $constant) {
				$apiConstant = new ReflectionConstant($constant);
				if ( ! $this->isDocumented() || $apiConstant->isDocumented()) {
					/** @var ReflectionElement $constant */
					$this->constants[$constant->getName()] = $apiConstant;
				}
			}
		}

		return $this->constants;
	}


	/**
	 * @return ReflectionConstant[]|array
	 */
	public function getOwnConstants()
	{
		if ($this->ownConstants === NULL) {
			$this->ownConstants = [];
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
	 * @param string $name
	 * @return ReflectionConstant
	 * @throws \InvalidArgumentException If required constant does not exist.
	 */
	public function getConstantReflection($name)
	{
		if ($this->constants === NULL) {
			$this->getConstants();
		}

		if (isset($this->constants[$name])) {
			return $this->constants[$name];
		}

		throw new InvalidArgumentException(sprintf(
			'Constant %s does not exist in class %s', $name, $this->reflection->getName()
		));
	}


	/**
	 * @param string $name
	 * @return ReflectionConstant
	 */
	public function getConstant($name)
	{
		return $this->getConstantReflection($name);
	}


	/**
	 * @param string $constantName
	 * @return bool
	 */
	public function hasConstant($constantName)
	{
		if ($this->constants === NULL) {
			$this->getConstants();
		}

		return isset($this->constants[$constantName]);
	}


	/**
	 * @param string $constantName
	 * @return bool
	 */
	public function hasOwnConstant($constantName)
	{
		if ($this->ownConstants === NULL) {
			$this->getOwnConstants();
		}

		return isset($this->ownConstants[$constantName]);
	}


	/**
	 * @param string $name
	 * @return ReflectionConstant
	 * @throws \InvalidArgumentException If required constant does not exist.
	 */
	public function getOwnConstantReflection($name)
	{
		if ($this->ownConstants === NULL) {
			$this->getOwnConstants();
		}

		if (isset($this->ownConstants[$name])) {
			return $this->ownConstants[$name];
		}

		throw new InvalidArgumentException(sprintf(
			'Constant %s does not exist in class %s', $name, $this->reflection->getName()
		));
	}


	/**
	 * @param string $name
	 * @return ReflectionConstant
	 */
	public function getOwnConstant($name)
	{
		return $this->getOwnConstantReflection($name);
	}


	/**
	 * @return ReflectionClass
	 */
	public function getParentClass()
	{
		if ($className = $this->reflection->getParentClassName()) {
			return $this->getParsedClasses()[$className];
		}
		return $className;
	}


	/**
	 * @return string|NULL
	 */
	public function getParentClassName()
	{
		return $this->reflection->getParentClassName();
	}


	/**
	 * @return ReflectionClass[]|array
	 */
	public function getParentClasses()
	{
		if ($this->parentClasses === NULL) {
			$this->parentClasses = array_map(function (IReflectionClass $class) {
				return ParserResult::$classesStatic[$class->getName()];
			}, $this->reflection->getParentClasses());
		}
		return $this->parentClasses;
	}


	/**
	 * @return array
	 */
	public function getParentClassNameList()
	{
		return $this->reflection->getParentClassNameList();
	}


	/**
	 * @param string|object $interface
	 * @return bool
	 */
	public function implementsInterface($interface)
	{
		return $this->reflection->implementsInterface($interface);
	}


	/**
	 * @return array
	 */
	public function getInterfaces()
	{
		return array_map(function (IReflectionClass $class) {
			return ParserResult::$classesStatic[$class->getName()];
		}, $this->reflection->getInterfaces());
	}


	/**
	 * @return array
	 */
	public function getInterfaceNames()
	{
		return $this->reflection->getInterfaceNames();
	}


	/**
	 * @return array
	 */
	public function getOwnInterfaces()
	{
		return array_map(function (IReflectionClass $class) {
			return ParserResult::$classesStatic[$class->getName()];
		}, $this->reflection->getOwnInterfaces());
	}


	/**
	 * @return array
	 */
	public function getOwnInterfaceNames()
	{
		return $this->reflection->getOwnInterfaceNames();
	}


	/**
	 * @return ReflectionClass[]|string[]
	 */
	public function getTraits()
	{
		return array_map(function (IReflectionClass $class) {
			if ( ! isset(ParserResult::$classesStatic[$class->getName()])) {
				return $class->getName();

			} else {
				return ParserResult::$classesStatic[$class->getName()];
			}
		}, $this->reflection->getTraits());
	}


	/**
	 * @return array
	 */
	public function getTraitNames()
	{
		return $this->reflection->getTraitNames();
	}


	/**
	 * @return array
	 */
	public function getOwnTraitNames()
	{
		return $this->reflection->getOwnTraitNames();
	}


	/**
	 * @return array
	 */
	public function getTraitAliases()
	{
		return $this->reflection->getTraitAliases();
	}


	/**
	 * @return ReflectionClass[]|string[]
	 */
	public function getOwnTraits()
	{
		return array_map(function (IReflectionClass $class) {
			if ( ! isset(ParserResult::$classesStatic[$class->getName()])) {
				return $class->getName();

			} else {
				return ParserResult::$classesStatic[$class->getName()];
			}
		}, $this->reflection->getOwnTraits());
	}


	/**
	 * @return bool
	 */
	public function isTrait()
	{
		return $this->reflection->isTrait();
	}


	/**
	 * @param string $trait
	 * @return bool
	 */
	public function usesTrait($trait)
	{
		return $this->reflection->usesTrait($trait);
	}


	/**
	 * @return array
	 */
	public function getDirectSubClasses()
	{
		$subClasses = [];
		$name = $this->reflection->getName();
		foreach ($this->getParsedClasses() as $class) {
			if ( ! $class->isDocumented()) {
				continue;
			}
			if ($name === $class->getParentClassName()) {
				$subClasses[] = $class;
			}
		}
		uksort($subClasses, 'strcasecmp');
		return $subClasses;
	}


	/**
	 * @return array
	 */
	public function getIndirectSubClasses()
	{
		$subClasses = [];
		$name = $this->reflection->getName();
		foreach ($this->getParsedClasses() as $class) {
			if ( ! $class->isDocumented()) {
				continue;
			}
			if ($name !== $class->getParentClassName() && $class->isSubclassOf($name)) {
				$subClasses[] = $class;
			}
		}
		uksort($subClasses, 'strcasecmp');
		return $subClasses;
	}


	/**
	 * @return array
	 */
	public function getDirectImplementers()
	{
		if ( ! $this->isInterface()) {
			return [];
		}

		$implementers = [];
		$name = $this->reflection->getName();
		foreach ($this->getParsedClasses() as $class) {
			if ( ! $class->isDocumented()) {
				continue;
			}
			if (in_array($name, $class->getOwnInterfaceNames())) {
				$implementers[] = $class;
			}
		}
		uksort($implementers, 'strcasecmp');
		return $implementers;
	}


	/**
	 * @return array
	 */
	public function getIndirectImplementers()
	{
		if ( ! $this->isInterface()) {
			return [];
		}

		$implementers = [];
		$name = $this->reflection->getName();
		foreach ($this->getParsedClasses() as $class) {
			if ( ! $class->isDocumented()) {
				continue;
			}
			if ($class->implementsInterface($name) && ! in_array($name, $class->getOwnInterfaceNames())) {
				$implementers[] = $class;
			}
		}
		uksort($implementers, 'strcasecmp');
		return $implementers;
	}


	/**
	 * @return array
	 */
	public function getDirectUsers()
	{
		if ( ! $this->isTrait()) {
			return [];
		}

		$users = [];
		$name = $this->reflection->getName();
		foreach ($this->getParsedClasses() as $class) {
			if ( ! $class->isDocumented()) {
				continue;
			}

			if (in_array($name, $class->getOwnTraitNames())) {
				$users[] = $class;
			}
		}
		uksort($users, 'strcasecmp');
		return $users;
	}


	/**
	 * @return array
	 */
	public function getIndirectUsers()
	{
		if ( ! $this->isTrait()) {
			return [];
		}

		$users = [];
		$name = $this->reflection->getName();
		foreach ($this->getParsedClasses() as $class) {
			if ( ! $class->isDocumented()) {
				continue;
			}
			if ($class->usesTrait($name) && ! in_array($name, $class->getOwnTraitNames())) {
				$users[] = $class;
			}
		}
		uksort($users, 'strcasecmp');
		return $users;
	}


	/**
	 * @return array
	 */
	public function getInheritedMethods()
	{
		$methods = [];
		$allMethods = array_flip(array_map(function ($method) {
			/** @var ReflectionMethod $method */
			return $method->getName();
		}, $this->getOwnMethods()));

		foreach (array_merge($this->getParentClasses(), $this->getInterfaces()) as $class) {
			/** @var ReflectionClass $class */
			$inheritedMethods = [];
			foreach ($class->getOwnMethods() as $method) {
				if ( ! array_key_exists($method->getName(), $allMethods) && ! $method->isPrivate()) {
					$inheritedMethods[$method->getName()] = $method;
					$allMethods[$method->getName()] = NULL;
				}
			}

			if ( ! empty($inheritedMethods)) {
				ksort($inheritedMethods);
				$methods[$class->getName()] = array_values($inheritedMethods);
			}
		}

		return $methods;
	}


	/**
	 * @return array
	 */
	public function getInheritedMagicMethods()
	{
		$methods = [];
		$allMethods = array_flip(array_map(function ($method) {
			/** @var ReflectionMethod $method */
			return $method->getName();
		}, $this->getOwnMagicMethods()));

		foreach (array_merge($this->getParentClasses(), $this->getInterfaces()) as $class) {
			/** @var ReflectionClass $class */
			$inheritedMethods = [];
			foreach ($class->getOwnMagicMethods() as $method) {
				if ( ! array_key_exists($method->getName(), $allMethods)) {
					$inheritedMethods[$method->getName()] = $method;
					$allMethods[$method->getName()] = NULL;
				}
			}

			if ( ! empty($inheritedMethods)) {
				ksort($inheritedMethods);
				$methods[$class->getName()] = array_values($inheritedMethods);
			}
		}

		return $methods;
	}


	/**
	 * @return array
	 */
	public function getUsedMethods()
	{
		$usedMethods = [];
		foreach ($this->getMethods() as $method) {
			if ($method->getDeclaringTraitName() === NULL || $method->getDeclaringTraitName() === $this->getName()) {
				continue;
			}

			$usedMethods[$method->getDeclaringTraitName()][$method->getName()]['method'] = $method;
			if ($method->getOriginalName() !== NULL && $method->getOriginalName() !== $method->getName()) {
				$usedMethods[$method->getDeclaringTraitName()][$method->getName()]['aliases'][$method->getName()] = $method;
			}
		}

		// Sort
		array_walk($usedMethods, function (&$methods) {
			ksort($methods);
			array_walk($methods, function (&$aliasedMethods) {
				if ( ! isset($aliasedMethods['aliases'])) {
					$aliasedMethods['aliases'] = [];
				}
				ksort($aliasedMethods['aliases']);
			});
		});

		return $usedMethods;
	}


	/**
	 * @return array
	 */
	public function getUsedMagicMethods()
	{
		$usedMethods = [];

		foreach ($this->getMagicMethods() as $method) {
			if ($method->getDeclaringTraitName() === NULL || $method->getDeclaringTraitName() === $this->getName()) {
				continue;
			}

			$usedMethods[$method->getDeclaringTraitName()][$method->getName()]['method'] = $method;
		}

		// Sort
		array_walk($usedMethods, function (&$methods) {
			ksort($methods);
			array_walk($methods, function (&$aliasedMethods) {
				if ( ! isset($aliasedMethods['aliases'])) {
					$aliasedMethods['aliases'] = [];
				}
				ksort($aliasedMethods['aliases']);
			});
		});

		return $usedMethods;
	}


	/**
	 * @return array
	 */
	public function getInheritedConstants()
	{
		return array_filter(
			array_map(
				function (ReflectionClass $class) {
					$reflections = $class->getOwnConstants();
					ksort($reflections);
					return $reflections;
				},
				array_merge($this->getParentClasses(), $this->getInterfaces())
			)
		);
	}


	/**
	 * Returns an array of inherited properties from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedProperties()
	{
		$properties = [];
		$allProperties = array_flip(array_map(function ($property) {
			/** @var ReflectionProperty $property */
			return $property->getName();
		}, $this->getOwnProperties()));

		foreach ($this->getParentClasses() as $class) {
			$inheritedProperties = [];
			foreach ($class->getOwnProperties() as $property) {
				if ( ! array_key_exists($property->getName(), $allProperties) && ! $property->isPrivate()) {
					$inheritedProperties[$property->getName()] = $property;
					$allProperties[$property->getName()] = NULL;
				}
			}

			if ( ! empty($inheritedProperties)) {
				ksort($inheritedProperties);
				$properties[$class->getName()] = array_values($inheritedProperties);
			}
		}

		return $properties;
	}


	/**
	 * Returns an array of inherited magic properties from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedMagicProperties()
	{
		$properties = [];
		$allProperties = array_flip(array_map(function ($property) {
			/** @var ReflectionProperty $property */
			return $property->getName();
		}, $this->getOwnMagicProperties()));

		foreach ($this->getParentClasses() as $class) {
			$inheritedProperties = [];
			foreach ($class->getOwnMagicProperties() as $property) {
				if ( ! array_key_exists($property->getName(), $allProperties)) {
					$inheritedProperties[$property->getName()] = $property;
					$allProperties[$property->getName()] = NULL;
				}
			}

			if ( ! empty($inheritedProperties)) {
				ksort($inheritedProperties);
				$properties[$class->getName()] = array_values($inheritedProperties);
			}
		}

		return $properties;
	}


	/**
	 * Returns an array of used properties from used traits grouped by the declaring trait name.
	 *
	 * @return array
	 */
	public function getUsedProperties()
	{
		$properties = [];
		$allProperties = array_flip(array_map(function ($property) {
			/** @var ReflectionProperty $property */
			return $property->getName();
		}, $this->getOwnProperties()));

		foreach ($this->getTraits() as $trait) {
			if ( ! $trait instanceof ReflectionClass) {
				continue;
			}

			$usedProperties = [];
			foreach ($trait->getOwnProperties() as $property) {
				if ( ! array_key_exists($property->getName(), $allProperties)) {
					$usedProperties[$property->getName()] = $property;
					$allProperties[$property->getName()] = NULL;
				}
			}

			if ( ! empty($usedProperties)) {
				ksort($usedProperties);
				$properties[$trait->getName()] = array_values($usedProperties);
			}
		}

		return $properties;
	}


	/**
	 * Returns an array of used magic properties from used traits grouped by the declaring trait name.
	 *
	 * @return array
	 */
	public function getUsedMagicProperties()
	{
		$properties = [];
		$allProperties = array_flip(array_map(function ($property) {
			/** @var ReflectionProperty $property */
			return $property->getName();
		}, $this->getOwnMagicProperties()));

		foreach ($this->getTraits() as $trait) {
			if ( ! $trait instanceof ReflectionClass) {
				continue;
			}

			$usedProperties = [];
			foreach ($trait->getOwnMagicProperties() as $property) {
				if ( ! array_key_exists($property->getName(), $allProperties)) {
					$usedProperties[$property->getName()] = $property;
					$allProperties[$property->getName()] = NULL;
				}
			}

			if ( ! empty($usedProperties)) {
				ksort($usedProperties);
				$properties[$trait->getName()] = array_values($usedProperties);
			}
		}

		return $properties;
	}


	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function hasProperty($propertyName)
	{
		if ($this->properties === NULL) {
			$this->getProperties();
		}
		return isset($this->properties[$propertyName]);
	}


	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function hasOwnProperty($propertyName)
	{
		if ($this->ownProperties === NULL) {
			$this->getOwnProperties();
		}
		return isset($this->ownProperties[$propertyName]);
	}


	/**
	 * @param string $propertyName
	 * @return bool
	 */
	public function hasTraitProperty($propertyName)
	{
		$properties = $this->getTraitProperties();
		return isset($properties[$propertyName]);
	}


	/**
	 * @param string $methodName
	 * @return bool
	 */
	public function hasMethod($methodName)
	{
		if ($this->methods === NULL) {
			$this->getMethods();
		}
		return isset($this->methods[$methodName]);
	}


	/**
	 * @param string $methodName
	 * @return bool
	 */
	public function hasOwnMethod($methodName)
	{
		if ($this->ownMethods === NULL) {
			$this->getOwnMethods();
		}
		return isset($this->ownMethods[$methodName]);
	}


	/**
	 * @param string $methodName Method name
	 * @return bool
	 */
	public function hasTraitMethod($methodName)
	{
		$methods = $this->getTraitMethods();
		return isset($methods[$methodName]);
	}


	/**
	 * @return bool
	 */
	public function isValid()
	{
		if ($this->reflection instanceof TokenReflection\Invalid\ReflectionClass) {
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * Returns if the class should be documented.
	 *
	 * @return bool
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL && parent::isDocumented()) {
			$fileName = $this->reflection->getFilename();
			foreach (self::$config->skipDocPath as $mask) {
				if (fnmatch($mask, $fileName, FNM_NOESCAPE)) {
					$this->isDocumented = FALSE;
					break;
				}
			}
		}

		return $this->isDocumented;
	}


	/**
	 * @return ArrayHash
	 */
	public function getConfig()
	{
		return Configuration::$config;
	}


	/**
	 * @return ArrayObject
	 */
	private function getParsedClasses()
	{
		if (self::$parsedClasses === NULL) {
			self::$parsedClasses = ParserResult::$classesStatic;
		}
		return self::$parsedClasses;
	}


	/**
	 * @param string $annotation
	 */
	private function processAnnotation($annotation)
	{
		$pattern = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?(&)?\\s*(\\w+)\\s*\\(\\s*(.*)\\s*\\)\\s*(.*|$)~s';
		if ( ! preg_match($pattern, $annotation, $matches)) {
			// Wrong annotation format
			return;
		}

		list(, $returnTypeHint, $returnsReference, $name, $args, $shortDescription) = $matches;

		$doc = $this->getDocComment();
		$tmp = $annotation;
		if ($delimiter = strpos($annotation, "\n")) {
			$tmp = substr($annotation, 0, $delimiter);
		}

		$startLine = $this->getStartLine() + substr_count(substr($doc, 0, strpos($doc, $tmp)), "\n");
		$endLine = $startLine + substr_count($annotation, "\n");

		$method = new ReflectionMethodMagic(NULL);
		$method->setName($name)
			->setShortDescription(str_replace("\n", ' ', $shortDescription))
			->setStartLine($startLine)
			->setEndLine($endLine)
			->setReturnsReference($returnsReference === '&')
			->setDeclaringClass($this)
			->addAnnotation('return', $returnTypeHint);

		$this->ownMagicMethods[$name] = $method;

		$parameters = [];
		foreach (array_filter(preg_split('~\\s*,\\s*~', $args)) as $position => $arg) {
			$pattern = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?(&)?\\s*\\$(\\w+)(?:\\s*=\\s*(.*))?($)~s';
			if ( ! preg_match($pattern, $arg, $matches)) {
				// Wrong annotation format
				continue;
			}

			list(, $typeHint, $passedByReference, $name, $defaultValueDefinition) = $matches;

			$parameter = new ReflectionParameterMagic(NULL);
			$parameter->setName($name)
				->setPosition($position)
				->setTypeHint($typeHint)
				->setDefaultValueDefinition($defaultValueDefinition)
				->setUnlimited(FALSE)
				->setPassedByReference($passedByReference === '&')
				->setDeclaringFunction($method);

			$parameters[$name] = $parameter;

			$method->addAnnotation('param', ltrim(sprintf('%s $%s', $typeHint, $name)));
		}
		$method->setParameters($parameters);
	}


	/**
	 * @param string $annotation
	 * @param string $annotationName
	 */
	private function processMagicPropertyAnnotation($annotation, $annotationName)
	{
		$pattern = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?\\$(\\w+)(?:\\s+(.*))?($)~s';
		if ( ! preg_match($pattern, $annotation, $matches)) {
			// Wrong annotation format
			return;
		}

		list(, $typeHint, $name, $shortDescription) = $matches;

		$doc = $this->getDocComment();
		$tmp = $annotation;
		if ($delimiter = strpos($annotation, "\n")) {
			$tmp = substr($annotation, 0, $delimiter);
		}

		$startLine = $this->getStartLine() + substr_count(substr($doc, 0, strpos($doc, $tmp)), "\n");
		$endLine = $startLine + substr_count($annotation, "\n");

		$magicProperty = new ReflectionPropertyMagic(NULL);
		$magicProperty->setName($name)
			->setTypeHint($typeHint)
			->setShortDescription(str_replace("\n", ' ', $shortDescription))
			->setStartLine($startLine)
			->setEndLine($endLine)
			->setReadOnly($annotationName === 'property-read')
			->setWriteOnly($annotationName === 'property-write')
			->setDeclaringClass($this)
			->addAnnotation('var', $typeHint);

		$this->ownMagicProperties[$name] = $magicProperty;
	}

}
