<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\FileSystem\FileSystem;
use InvalidArgumentException;
use ReflectionMethod as InternalReflectionMethod;
use ReflectionProperty as InternalReflectionProperty;
use TokenReflection;


class ReflectionClass extends ReflectionElement
{

	/**
	 * @var array
	 */
	private $parentClasses = array();

	/**
	 * @var array
	 */
	private $ownMethods = array();

	/**
	 * @var array
	 */
	private $ownMagicMethods = array();

	/**
	 * @var array
	 */
	private $ownProperties = array();

	/**
	 * @var array
	 */
	private $ownMagicProperties = array();

	/**
	 * @var array
	 */
	private $ownConstants = array();

	/**
	 * @var array
	 */
	private $methods = array();

	/**
	 * @var array
	 */
	private $properties = array();

	/**
	 * @var array
	 */
	private $constants = array();

	/**
	 * @var array
	 */
	private $interfaces = array();


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
	 * Returns the unqualified name.
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
	 * Returns visible methods.
	 *
	 * @return ReflectionMethod[]|array
	 */
	public function getMethods()
	{
		if ($this->methods === NULL) {
			$this->methods = $this->getOwnMethods();
			foreach ($this->reflection->getMethods($this->getMethodAccessLevels()) as $method) {
				/** @var ReflectionElement|TokenReflection\Php\IReflection $method */
				if (isset($this->methods[$method->getName()])) {
					continue;
				}
				$apiMethod = $this->apiGenReflectionFactory->createFromReflection($method);
				if ( ! $this->isDocumented() || $apiMethod->isDocumented()) {
					$this->methods[$method->getName()] = $apiMethod;
				}
			}
		}
		return $this->methods;
	}


	/**
	 * Returns visible methods declared by inspected class.
	 *
	 * @return ReflectionMethod[]|array
	 */
	public function getOwnMethods()
	{
		if ( ! $this->ownMethods) {
			foreach ($this->reflection->getOwnMethods($this->getMethodAccessLevels()) as $method) {
				$apiMethod = $this->apiGenReflectionFactory->createFromReflection($method);
				if ( ! $this->isDocumented() || $apiMethod->isDocumented()) {
					$this->ownMethods[$method->getName()] = $apiMethod;
				}
			}
		}
		return $this->ownMethods;
	}


	/**
	 * Returns visible magic methods.
	 *
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
	 * Returns visible magic methods declared by inspected class.
	 *
	 * @return ReflectionMethod[]|array
	 */
	public function getOwnMagicMethods()
	{
		if ( ! $this->ownMagicMethods) {
			if ( ! ($this->getMethodAccessLevels() & InternalReflectionMethod::IS_PUBLIC)
				|| $this->getDocComment() === FALSE
			) {
				return $this->ownMagicMethods;
			}

			$annotations = $this->getAnnotation('method');
			if ($annotations === NULL) {
				return $this->ownMagicMethods;
			}

			foreach ($annotations as $annotation) {
				$matches = $this->matchMagicMethodAnnotation($annotation);
				if ( ! $matches) {
					continue;
				}

				list(, $returnTypeHint, $returnsReference, $name, $args, $shortDescription) = $matches;

				$doc = $this->getDocComment();
				$tmp = $annotation;
				if ($delimiter = strpos($annotation, "\n")) {
					$tmp = substr($annotation, 0, $delimiter);
				}

				$startLine = $this->getStartLine() + substr_count(substr($doc, 0, strpos($doc, $tmp)), "\n");
				$endLine = $startLine + substr_count($annotation, "\n");

				$method = $this->apiGenReflectionFactory->createMethodMagic()
					->setName($name)
					->setShortDescription(str_replace("\n", ' ', $shortDescription))
					->setStartLine($startLine)
					->setEndLine($endLine)
					->setReturnsReference($returnsReference === '&')
					->setDeclaringClass($this)
					->addAnnotation('return', $returnTypeHint);

				$this->ownMagicMethods[$name] = $method;

				$parameters = array();
				foreach (array_filter(preg_split('~\\s*,\\s*~', $args)) as $position => $arg) {
					$matches = $this->matchMagicPropertyAnnotation($arg);
					if ( ! $matches) {
						continue;
					}

					list(, $typeHint, $passedByReference, $name, $defaultValueDefinition) = $matches;

					if (empty($typeHint)) {
						$typeHint = 'mixed';
					}

					$parameter = $this->apiGenReflectionFactory->createParameterMagic()
						->setName($name)
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
		}
		return $this->ownMagicMethods;
	}


	/**
	 * Returns visible methods declared by traits.
	 *
	 * @return array
	 */
	public function getTraitMethods()
	{
		$methods = array();
		foreach ($this->reflection->getTraitMethods($this->getMethodAccessLevels()) as $method) {
			$apiMethod = $this->apiGenReflectionFactory->createFromReflection($method);
			if ( ! $this->isDocumented() || $apiMethod->isDocumented()) {
				/** @var ReflectionElement $method */
				$methods[$method->getName()] = $apiMethod;
			}
		}
		return $methods;
	}


	/**
	 * @param string $name
	 * @return ReflectionMethod
	 * @throws InvalidArgumentException If required method does not exist.
	 */
	public function getMethod($name)
	{
		if ($this->hasMethod($name)) {
			return $this->methods[$name];
		}

		throw new InvalidArgumentException("Method $name not found in class " . $this->reflection->getName());
	}


	/**
	 * Returns visible properties.
	 *
	 * @return array
	 */
	public function getProperties()
	{
		if ( ! $this->properties) {
			$this->properties = $this->getOwnProperties();
			foreach ($this->reflection->getProperties($this->getPropertyAccessLevels()) as $property) {
				/** @var ReflectionElement $property */
				if (isset($this->properties[$property->getName()])) {
					continue;
				}

				$apiProperty = $this->apiGenReflectionFactory->createFromReflection($property);
				if ( ! $this->isDocumented() || $apiProperty->isDocumented()) {
					$this->properties[$property->getName()] = $apiProperty;
				}
			}
		}
		return $this->properties;
	}


	/**
	 * Returns visible magic properties.
	 *
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
	 * Returns visible properties declared by inspected class.
	 *
	 * @return ReflectionProperty[]|array
	 */
	public function getOwnProperties()
	{
		if ( ! $this->ownProperties) {
			foreach ($this->reflection->getOwnProperties($this->getPropertyAccessLevels()) as $property) {
				$apiProperty = $this->apiGenReflectionFactory->createFromReflection($property);
				if ( ! $this->isDocumented() || $apiProperty->isDocumented()) {
					/** @var ReflectionElement $property */
					$this->ownProperties[$property->getName()] = $apiProperty;
				}
			}
		}
		return $this->ownProperties;
	}


	/**
	 * Returns visible properties magically declared by inspected class.
	 *
	 * @return ReflectionProperty[]|array
	 */
	public function getOwnMagicProperties()
	{
		if ( ! $this->ownMagicProperties) {
			if ( ! ($this->getPropertyAccessLevels() & InternalReflectionProperty::IS_PUBLIC)
				|| $this->getDocComment() === FALSE
			) {
				return $this->ownMagicProperties;
			}

			foreach (array('property', 'property-read', 'property-write') as $annotationName) {
				$annotations = $this->getAnnotation($annotationName);
				if ($annotations === NULL) {
					continue;
				}

				foreach ($annotations as $annotation) {
					if ( ! preg_match('~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?\\$(\\w+)(?:\\s+(.*))?($)~s', $annotation, $matches)) {
						// Wrong annotation format
						continue;
					}

					list(, $typeHint, $name, $shortDescription) = $matches;

					if (empty($typeHint)) {
						$typeHint = 'mixed';
					}

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
		}

		return $this->ownMagicProperties;
	}


	/**
	 * Returns visible properties declared by traits.
	 *
	 * @return array
	 */
	public function getTraitProperties()
	{
		$properties = array();
		foreach ($this->reflection->getTraitProperties($this->getPropertyAccessLevels()) as $property) {
			$apiProperty = $this->apiGenReflectionFactory->createFromReflection($property);
			if ( ! $this->isDocumented() || $apiProperty->isDocumented()) {
				/** @var ReflectionElement $property */
				$properties[$property->getName()] = $apiProperty;
			}
		}
		return $properties;
	}


	/**
	 * @param string $name
	 * @return ReflectionProperty
	 * @throws InvalidArgumentException If required property does not exist.
	 */
	public function getProperty($name)
	{
		if ($this->hasProperty($name)) {
			return $this->properties[$name];
		}

		throw new InvalidArgumentException("Property $name does not exist in class " . $this->reflection->getName());
	}


	/**
	 * Returns visible constants.
	 *
	 * @return ReflectionConstant[]|array
	 */
	public function getConstants()
	{
		if ( ! $this->constants) {
			foreach ($this->reflection->getConstantReflections() as $constant) {
				$apiConstant = $this->apiGenReflectionFactory->createFromReflection($constant);
				if ( ! $this->isDocumented() || $apiConstant->isDocumented()) {
					/** @var ReflectionElement $constant */
					$this->constants[$constant->getName()] = $apiConstant;
				}
			}
		}

		return $this->constants;
	}


	/**
	 * @return array
	 */
	public function getConstantReflections()
	{
		return $this->reflection->getConstantReflections();
	}


	/**
	 * Returns constants declared by inspected class.
	 *
	 * @return ReflectionConstant[]|array
	 */
	public function getOwnConstants()
	{
		if ( ! $this->ownConstants) {
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
	 * @param string $name Constant name
	 * @return ReflectionConstant
	 * @throws InvalidArgumentException If required constant does not exist.
	 */
	public function getConstantReflection($name)
	{
		if ($this->hasConstant($name)) {
			return $this->constants[$name];
		}

		throw new InvalidArgumentException("Constant $name does not exist in class " . $this->reflection->getName());
	}


	/**
	 * @param string $name
	 * @return ReflectionConstant
	 */
	public function getConstant($name)
	{
		// duplicate to getConstantReflection()
		return $this->getConstantReflection($name);
	}


	/**
	 * @param string $constantName
	 * @return bool
	 */
	public function hasConstant($constantName)
	{
		if ( ! $this->constants) {
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
		if ( ! $this->ownConstants) {
			$this->getOwnConstants();
		}

		return isset($this->ownConstants[$constantName]);
	}


	/**
	 * @param string $name
	 * @return ReflectionConstant
	 * @throws InvalidArgumentException If required constant does not exist.
	 */
	public function getOwnConstantReflection($name)
	{
		if ($this->hasOwnConstant($name)) {
			return $this->ownConstants[$name];
		}

		throw new InvalidArgumentException("Constant $name does not exist in class " . $this->reflection->getName());
	}


	/**
	 * @param string $name
	 * @return ReflectionConstant
	 */
	public function getOwnConstant($name)
	{
		// duplicate to getOwnConstantReflection
		return $this->getOwnConstantReflection($name);
	}


	/**
	 * Returns a parent class reflection encapsulated by this class.
	 *
	 * @return ReflectionClass
	 */
	public function getParentClass()
	{
		if ($className = $this->reflection->getParentClassName()) {
			$parsedClasses = $this->getParsedClasses();
			return $parsedClasses[$className];
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
		return $this->reflection->getParentClasses();
	}


	/**
	 * @return array
	 */
	public function getParentClassNameList()
	{
		return $this->reflection->getParentClassNameList();
	}


	/**
	 * @param string|object $interface Interface name or reflection object
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
		if ( ! $this->interfaces) {
			$classes = $this->getParsedClasses();
			$this->interfaces = array_map(function (ReflectionClass $class) use ($classes) {
				return $classes[$class->getName()];
			}, $this->reflection->getInterfaces());
		}
		return $this->interfaces;
	}


	/**
	 * @return array
	 */
	public function getInterfaceNames()
	{
		return $this->reflection->getInterfaceNames();
	}


	/**
	 * Returns all interfaces implemented by the inspected class and not its parents.
	 *
	 * @return array
	 */
	public function getOwnInterfaces()
	{
		$classes = $this->getParsedClasses();
		return array_map(function (ReflectionClass $class) use ($classes) {
			return $classes[$class->getName()];
		}, $this->reflection->getOwnInterfaces());
	}


	/**
	 * Returns names of interfaces implemented by this class, not its parents.
	 *
	 * @return array
	 */
	public function getOwnInterfaceNames()
	{
		return $this->reflection->getOwnInterfaceNames();
	}


	/**
	 * Returns all traits reflections encapsulated by this class.
	 *
	 * @return ReflectionClass[]|string[]
	 */
	public function getTraits()
	{
		$classes = $this->getParsedClasses();
		return array_map(function (ReflectionClass $class) use ($classes) {
			if ( ! isset($classes[$class->getName()])) {
				return $class->getName();

			} else {
				return $classes[$class->getName()];
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
	 * Returns names of traits used by this class an not its parents.
	 *
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
	 * Returns all traits used by the inspected class and not its parents.
	 *
	 * @return ReflectionClass[]|string[]
	 */
	public function getOwnTraits()
	{
		$classes = $this->getParsedClasses();
		return array_map(function (ReflectionClass $class) use ($classes) {
			if ( ! isset($classes[$class->getName()])) {
				return $class->getName();

			} else {
				return $classes[$class->getName()];
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
	 * Returns reflections of direct subclasses.
	 *
	 * @return array
	 */
	public function getDirectSubClasses()
	{
		$subClasses = array();
		$name = $this->reflection->getName();
		foreach ($this->getParsedClasses() as $class) {
			if ( ! $class->isDocumented()) {
				continue;
			}
			if ($class->getParentClassName() === $name) {
				$subClasses[] = $class;
			}
		}

		uksort($subClasses, 'strcasecmp');

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
		foreach ($this->getParsedClasses() as $class) {
			if ( ! $class->isDocumented()) {
				continue;
			}
			if ($class->getParentClassName() !== $name && $class->isSubclassOf($name)) {
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
			return array();
		}

		$implementers = array();
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
			return array();
		}

		$implementers = array();
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
			return array();
		}

		$users = array();
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
			return array();
		}

		$users = array();
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
	 * Returns an array of inherited methods from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedMethods()
	{
		$methods = array();
		$allMethods = array_flip(array_map(function ($method) {
			/** @var ReflectionMethod $method */
			return $method->getName();
		}, $this->getOwnMethods()));

		foreach (array_merge($this->getParentClasses(), $this->getInterfaces()) as $class) {
			/** @var ReflectionClass $class */
			$inheritedMethods = array();
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
	 * Returns an array of inherited magic methods from parent classes grouped by the declaring class name.
	 *
	 * @return array
	 */
	public function getInheritedMagicMethods()
	{
		$methods = array();
		$allMethods = array_flip(array_map(function ($method) {
			/** @var ReflectionMethod $method */
			return $method->getName();
		}, $this->getOwnMagicMethods()));

		foreach (array_merge($this->getParentClasses(), $this->getInterfaces()) as $class) {
			/** @var ReflectionClass $class */
			$inheritedMethods = array();
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
	 * Returns an array of used methods from used traits grouped by the declaring trait name.
	 *
	 * @return array
	 */
	public function getUsedMethods()
	{
		$usedMethods = array();
		foreach ($this->getMethods() as $method) {
			$traitName = $method->getDeclaringTraitName();
			if ($traitName === NULL || $traitName === $this->getName()) {
				continue;
			}

			$usedMethods[$traitName][$method->getName()]['method'] = $method;
			if ($method->getOriginalName() !== NULL && $method->getOriginalName() !== $method->getName()) {
				$usedMethods[$traitName][$method->getName()]['aliases'][$method->getName()] = $method;
			}
		}

		// Sort
		array_walk($usedMethods, function (&$methods) {
			ksort($methods);
			array_walk($methods, function (&$aliasedMethods) {
				if ( ! isset($aliasedMethods['aliases'])) {
					$aliasedMethods['aliases'] = array();
				}
				ksort($aliasedMethods['aliases']);
			});
		});

		return $usedMethods;
	}


	/**
	 * Returns an array of used magic methods from used traits grouped by the declaring trait name.
	 *
	 * @return array
	 */
	public function getUsedMagicMethods()
	{
		$usedMethods = array();

		foreach ($this->getMagicMethods() as $method) {
			$traitName = $method->getDeclaringTraitName();
			if ($traitName === NULL || $traitName === $this->getName()) {
				continue;
			}

			$usedMethods[$traitName][$method->getName()]['method'] = $method;
		}

		// Sort
		array_walk($usedMethods, function (&$methods) {
			ksort($methods);
			array_walk($methods, function (&$aliasedMethods) {
				if ( ! isset($aliasedMethods['aliases'])) {
					$aliasedMethods['aliases'] = array();
				}
				ksort($aliasedMethods['aliases']);
			});
		});

		return $usedMethods;
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
		$properties = array();
		$allProperties = array_flip(array_map(function ($property) {
			/** @var ReflectionProperty $property */
			return $property->getName();
		}, $this->getOwnProperties()));

		foreach ($this->getParentClasses() as $class) {
			$inheritedProperties = array();
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
		$properties = array();
		$allProperties = array_flip(array_map(function ($property) {
			/** @var ReflectionProperty $property */
			return $property->getName();
		}, $this->getOwnMagicProperties()));

		foreach ($this->getParentClasses() as $class) {
			$inheritedProperties = array();
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
		$properties = array();
		$allProperties = array_flip(array_map(function ($property) {
			/** @var ReflectionProperty $property */
			return $property->getName();
		}, $this->getOwnProperties()));

		foreach ($this->getTraits() as $trait) {
			if ( ! $trait instanceof ReflectionClass) {
				continue;
			}

			$usedProperties = array();
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
		$properties = array();
		$allProperties = array_flip(array_map(function ($property) {
			/** @var ReflectionProperty $property */
			return $property->getName();
		}, $this->getOwnMagicProperties()));

		foreach ($this->getTraits() as $trait) {
			if ( ! $trait instanceof ReflectionClass) {
				continue;
			}

			$usedProperties = array();
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
	 * @param string $name
	 * @return bool
	 */
	public function hasProperty($name)
	{
		if ( ! $this->properties) {
			$this->getProperties();
		}
		return isset($this->properties[$name]);
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasOwnProperty($name)
	{
		if ( ! $this->ownProperties) {
			$this->getOwnProperties();
		}
		return isset($this->ownProperties[$name]);
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasTraitProperty($name)
	{
		$properties = $this->getTraitProperties();
		return isset($properties[$name]);
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasMethod($name)
	{
		if ( ! $this->methods) {
			$this->getMethods();
		}
		return isset($this->methods[$name]);
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasOwnMethod($name)
	{
		if ( ! $this->ownMethods) {
			$this->getOwnMethods();
		}
		return isset($this->ownMethods[$name]);
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
	 * @return bool
	 */
	public function isComplete()
	{
		return $this->reflection->isComplete();
	}


	/**
	 * Returns if the class should be documented.
	 *
	 * @return bool
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL && parent::isDocumented()) {
			$fileName = FileSystem::unPharPath($this->reflection->getFilename());
			$options = $this->configuration->getOptions();
			foreach ($options['skipDocPath'] as $mask) {
				if (fnmatch($mask, $fileName, FNM_NOESCAPE)) {
					$this->isDocumented = FALSE;
					break;
				}
			}
			if ($this->isDocumented === TRUE) {
				foreach ($options['skipDocPrefix'] as $prefix) {
					if (strpos($this->reflection->getName(), $prefix) === 0) {
						$this->isDocumented = FALSE;
						break;
					}
				}
			}
		}

		return $this->isDocumented;
	}


	/**
	 * @param string $s
	 * @return bool|array
	 */
	private function matchMagicMethodAnnotation($s)
	{
		$mask = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?(&)?\\s*(\\w+)\\s*\\(\\s*(.*)\\s*\\)\\s*(.*|$)~s';
		if (preg_match($mask, $s, $matches)) {
			return $matches;
		}
		return FALSE;
	}


	/**
	 * @param string $s
	 * @return bool
	 */
	private function matchMagicPropertyAnnotation($s)
	{
		$mask = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?(&)?\\s*\\$(\\w+)(?:\\s*=\\s*(.*))?($)~s';
		if (preg_match($mask, $s, $matches)) {
			return $matches;
		}
		return FALSE;
	}


	/**
	 * @return int
	 */
	private function getPropertyAccessLevels()
	{
		return $this->configuration->getOption('propertyAccessLevels');
	}


	/**
	 * @return int
	 */
	private function getMethodAccessLevels()
	{
		return $this->configuration->getOption('methodAccessLevels');
	}

}
