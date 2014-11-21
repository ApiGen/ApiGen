<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Configuration\Configuration;
use ApiGen\FileSystem\FileSystem;
use ApiGen\Parser\ParserResult;
use ArrayObject;
use InvalidArgumentException;
use Nette\Utils\ArrayHash;
use ReflectionMethod as InternalReflectionMethod;
use ReflectionProperty as InternalReflectionProperty;
use TokenReflection;
use TokenReflection\IReflectionClass;


/**
 * Alters TokenReflection\IReflectionClass functionality for ApiGen.
 */
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
		$this->setAccessLevels();
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
	 * Returns if the class is an exception or its descendant.
	 *
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
	 * Returns visible methods declared by inspected class.
	 *
	 * @return ReflectionMethod[]|array
	 */
	public function getOwnMethods()
	{
		if ($this->ownMethods === NULL) {
			$this->ownMethods = array();
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
		if ($this->ownMagicMethods === NULL) {
			$this->ownMagicMethods = array();

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
				$pattern = '~^(?:([\\w\\\\]+(?:\\|[\\w\\\\]+)*)\\s+)?(&)?\\s*(\\w+)\\s*\\(\\s*(.*)\\s*\\)\\s*(.*|$)~s';
				if ( ! preg_match($pattern, $annotation, $matches)) {
					// Wrong annotation format
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

				$method = new ReflectionMethodMagic(NULL);
				$method->setName($name)
					->setShortDescription(str_replace("\n", ' ', $shortDescription))
					->setStartLine($startLine)
					->setEndLine($endLine)
					->setReturnsReference($returnsReference === '&')
					->setDeclaringClass($this)
					->addAnnotation('return', $returnTypeHint);

				$this->ownMagicMethods[$name] = $method;

				$parameters = array();
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
	 * Returns a method reflection.
	 *
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
	 * Returns visible properties.
	 *
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
		if ($this->ownProperties === NULL) {
			$this->ownProperties = array();
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
	 * Returns visible properties magicly declared by inspected class.
	 *
	 * @return ReflectionProperty[]|array
	 */
	public function getOwnMagicProperties()
	{
		if ($this->ownMagicProperties === NULL) {
			$this->ownMagicProperties = array();

			if ( ! (self::$propertyAccessLevels & InternalReflectionProperty::IS_PUBLIC) || $this->getDocComment() === FALSE) {
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
	 * Returns a method property.
	 *
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
	 * Returns visible properties.
	 *
	 * @return ReflectionConstant[]|array
	 */
	public function getConstants()
	{
		if ($this->constants === NULL) {
			$this->constants = array();
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
	 * Returns constants declared by inspected class.
	 *
	 * @return ReflectionConstant[]|array
	 */
	public function getOwnConstants()
	{
		if ($this->ownConstants === NULL) {
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
	 * Returns a constant reflection.
	 *
	 * @param string $name Constant name
	 * @return ReflectionConstant
	 */
	public function getConstant($name)
	{
		return $this->getConstantReflection($name);
	}


	/**
	 * Checks if there is a constant of the given name.
	 *
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
	 * Checks if there is a constant of the given name.
	 *
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
	 * Returns a constant reflection.
	 *
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
	 * Returns a constant reflection.
	 *
	 * @param string $name
	 * @return ReflectionConstant
	 */
	public function getOwnConstant($name)
	{
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
	 * Returns the parent class name.
	 *
	 * @return string|NULL
	 */
	public function getParentClassName()
	{
		return $this->reflection->getParentClassName();
	}


	/**
	 * Returns all parent classes reflections encapsulated by this class.
	 *
	 * @return ReflectionClass[]|array
	 */
	public function getParentClasses()
	{
		if ($this->parentClasses === NULL) {
			$classes = ParserResult::$classes;
			$this->parentClasses = array_map(function (IReflectionClass $class) use ($classes) {
				return $classes[$class->getName()];
			}, $this->reflection->getParentClasses());
		}
		return $this->parentClasses;
	}


	/**
	 * Returns the parent classes names.
	 *
	 * @return array
	 */
	public function getParentClassNameList()
	{
		return $this->reflection->getParentClassNameList();
	}


	/**
	 * Returns if the class implements the given interface.
	 *
	 * @param string|object $interface Interface name or reflection object
	 * @return bool
	 */
	public function implementsInterface($interface)
	{
		return $this->reflection->implementsInterface($interface);
	}


	/**
	 * Returns all interface reflections encapsulated by this class.
	 *
	 * @return array
	 */
	public function getInterfaces()
	{
		$classes = ParserResult::$classes;
		return array_map(function (IReflectionClass $class) use ($classes) {
			return $classes[$class->getName()];
		}, $this->reflection->getInterfaces());
	}


	/**
	 * Returns interface names.
	 *
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
		$classes = ParserResult::$classes;
		return array_map(function (IReflectionClass $class) use ($classes) {
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
		$classes = ParserResult::$classes;
		return array_map(function (IReflectionClass $class) use ($classes) {
			if ( ! isset($classes[$class->getName()])) {
				return $class->getName();

			} else {
				return $classes[$class->getName()];
			}
		}, $this->reflection->getTraits());
	}


	/**
	 * Returns names of used traits.
	 *
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
	 * Returns method aliases from traits.
	 *
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
		$classes = ParserResult::$classes;
		return array_map(function (IReflectionClass $class) use ($classes) {
			if ( ! isset($classes[$class->getName()])) {
				return $class->getName();

			} else {
				return $classes[$class->getName()];
			}
		}, $this->reflection->getOwnTraits());
	}


	/**
	 * Returns if the class is a trait.
	 *
	 * @return bool
	 */
	public function isTrait()
	{
		return $this->reflection->isTrait();
	}


	/**
	 * Returns if the class uses a particular trait.
	 *
	 * @param string $trait Trait name
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
			if ($name === $class->getParentClassName()) {
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
		foreach ($this->getParsedClasses() as $class) {
			if ( ! $class->isDocumented()) {
				continue;
			}
			if ($name !== $class->getParentClassName() && $class->isSubclassOf($name)) {
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
		return $implementers;
	}


	/**
	 * Returns reflections of classes indirectly implementing this interface.
	 *
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
		return $implementers;
	}


	/**
	 * Returns reflections of classes directly using this trait.
	 *
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
		return $users;
	}


	/**
	 * Returns reflections of classes indirectly using this trait.
	 *
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
			$fileName = FileSystem::unPharPath($this->reflection->getFilename());
			foreach (self::$config->skipDocPath as $mask) {
				if (fnmatch($mask, $fileName, FNM_NOESCAPE)) {
					$this->isDocumented = FALSE;
					break;
				}
			}
			if ($this->isDocumented === TRUE) {
				foreach (self::$config->skipDocPrefix as $prefix) {
					if (strpos($this->reflection->getName(), $prefix) === 0) {
						$this->isDocumented = FALSE;
						break;
					}
				}
			}
		}

		return $this->isDocumented;
	}


	private function setAccessLevels()
	{
		foreach (Configuration::$config->accessLevels as $level) {
			if ($level === 'public') {
				self::$methodAccessLevels |= InternalReflectionMethod::IS_PUBLIC;
				self::$propertyAccessLevels |= InternalReflectionProperty::IS_PUBLIC;

			} elseif ($level === 'protected') {
				self::$methodAccessLevels |= InternalReflectionMethod::IS_PROTECTED;
				self::$propertyAccessLevels |= InternalReflectionProperty::IS_PROTECTED;

			} elseif ($level === 'private') {
				self::$methodAccessLevels |= InternalReflectionMethod::IS_PRIVATE;
				self::$propertyAccessLevels |= InternalReflectionProperty::IS_PRIVATE;
			}
		}
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
			self::$parsedClasses = ParserResult::$classes;
		}
		return self::$parsedClasses;
	}

}
