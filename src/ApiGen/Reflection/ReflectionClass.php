<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Reflection\Extractors\AnnotationMethodExtractor;
use ApiGen\Reflection\Extractors\AnnotationPropertyExtractor;
use ApiGen\Reflection\Extractors\MagicMethodExtractor;
use ApiGen\Reflection\Extractors\MagicPropertyExtractor;
use InvalidArgumentException;
use ReflectionProperty as Visibility;
use TokenReflection;
use TokenReflection\IReflectionClass;


class ReflectionClass extends ReflectionElement
{

	/**
	 * @var ReflectionClass[]
	 */
	private $parentClasses;

	/**
	 * @var ReflectionProperty[]
	 */
	private $properties;

	/**
	 * @var ReflectionProperty[]
	 */
	private $ownProperties;

	/**
	 * @var ReflectionPropertyMagic[]
	 */
	private $ownMagicProperties;

	/**
	 * @var ReflectionConstant[]
	 */
	private $constants;

	/**
	 * @var ReflectionConstant[]
	 */
	private $ownConstants;

	/**
	 * @var ReflectionMethod[]
	 */
	private $methods;

	/**
	 * @var ReflectionMethod[]
	 */
	private $ownMethods;

	/**
	 * @var ReflectionMethodMagic[]
	 */
	private $ownMagicMethods;


	/**
	 * @return string
	 */
	public function getShortName()
	{
		return $this->reflection->getShortName();
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
	 * @return ReflectionMethod[]
	 */
	public function getMethods()
	{
		if ($this->methods === NULL) {
			$this->methods = $this->getOwnMethods();

			foreach ($this->reflection->getMethods($this->getVisibilityLevel()) as $method) {
				/** @var ReflectionElement|TokenReflection\Php\IReflection $method */
				if (isset($this->methods[$method->getName()])) {
					continue;
				}
				$apiMethod = $this->reflectionFactory->createFromReflection($method);
				if ( ! $this->isDocumented() || $apiMethod->isDocumented()) {
					$this->methods[$method->getName()] = $apiMethod;
				}
			}
		}
		return $this->methods;
	}


	/**
	 * @return ReflectionMethod[]
	 */
	public function getOwnMethods()
	{
		if ($this->ownMethods === NULL) {
			$this->ownMethods = [];

			foreach ($this->reflection->getOwnMethods($this->getVisibilityLevel()) as $method) {
				$apiMethod = $this->reflectionFactory->createFromReflection($method);
				if ( ! $this->isDocumented() || $apiMethod->isDocumented()) {
					$this->ownMethods[$method->getName()] = $apiMethod;
				}
			}
		}
		return $this->ownMethods;
	}


	/**
	 * @return ReflectionMethodMagic[]
	 */
	public function getMagicMethods()
	{
		return $this->getOwnMagicMethods() + (new MagicMethodExtractor)->extractFromClass($this);
	}


	/**
	 * @return ReflectionMethodMagic[]
	 */
	public function getOwnMagicMethods()
	{
		if ($this->ownMagicMethods === NULL) {
			$this->ownMagicMethods = [];

			if ($this->isVisibilityLevelPublic() && $this->getDocComment()) {
				$extractor = new AnnotationMethodExtractor($this->reflectionFactory);
				$this->ownMagicMethods += $extractor->extractFromReflection($this);
			}
		}
		return $this->ownMagicMethods;
	}


	/**
	 * @return ReflectionMethod[]
	 */
	public function getTraitMethods()
	{
		$methods = [];
		foreach ($this->reflection->getTraitMethods($this->getVisibilityLevel()) as $method) {
			$apiMethod = $this->reflectionFactory->createFromReflection($method);
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
	 * @return ReflectionProperty[]
	 */
	public function getProperties()
	{
		if ($this->properties === NULL) {
			$this->properties = $this->getOwnProperties();
			foreach ($this->reflection->getProperties($this->getVisibilityLevel()) as $property) {
				/** @var ReflectionElement $property */
				if (isset($this->properties[$property->getName()])) {
					continue;
				}
				$apiProperty = $this->reflectionFactory->createFromReflection($property);
				if ( ! $this->isDocumented() || $apiProperty->isDocumented()) {
					$this->properties[$property->getName()] = $apiProperty;
				}
			}
		}
		return $this->properties;
	}


	/**
	 * @return ReflectionPropertyMagic[]
	 */
	public function getMagicProperties()
	{
		return $this->getOwnMagicProperties() + (new MagicPropertyExtractor)->extractFromClass($this);
	}


	/**
	 * @return ReflectionPropertyMagic[]|array
	 */
	public function getOwnMagicProperties()
	{
		if ($this->ownMagicProperties === NULL) {
			$this->ownMagicProperties = [];

			if ($this->isVisibilityLevelPublic() && $this->getDocComment()) {
				$extractor = new AnnotationPropertyExtractor($this->reflectionFactory);
				$this->ownMagicProperties += $extractor->extractFromReflection($this);
			}
		}

		return $this->ownMagicProperties;
	}


	/**
	 * @return ReflectionProperty[]
	 */
	public function getOwnProperties()
	{
		if ($this->ownProperties === NULL) {
			$this->ownProperties = [];
			foreach ($this->reflection->getOwnProperties($this->getVisibilityLevel()) as $property) {
				$apiProperty = $this->reflectionFactory->createFromReflection($property);
				if ( ! $this->isDocumented() || $apiProperty->isDocumented()) {
					/** @var ReflectionElement $property */
					$this->ownProperties[$property->getName()] = $apiProperty;
				}
			}
		}
		return $this->ownProperties;
	}


	/**
	 * @return ReflectionProperty[]
	 */
	public function getTraitProperties()
	{
		$properties = [];
		foreach ($this->reflection->getTraitProperties($this->getVisibilityLevel()) as $property) {
			$apiProperty = $this->reflectionFactory->createFromReflection($property);
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
	 * @return ReflectionConstant[]
	 */
	public function getConstants()
	{
		if ($this->constants === NULL) {
			$this->constants = [];
			foreach ($this->reflection->getConstantReflections() as $constant) {
				$apiConstant = $this->reflectionFactory->createFromReflection($constant);
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
	 */
	public function getConstantReflection($name)
	{
		if (isset($this->getConstants()[$name])) {
			return $this->getConstants()[$name];
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
	 * @param string $name
	 * @return bool
	 */
	public function hasConstant($name)
	{
		return isset($this->getConstants()[$name]);
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasOwnConstant($name)
	{
		return isset($this->getOwnConstants()[$name]);
	}


	/**
	 * @param string $name
	 * @return ReflectionConstant
	 */
	public function getOwnConstant($name)
	{
		if (isset($this->getOwnConstants()[$name])) {
			return $this->getOwnConstants()[$name];
		}

		throw new InvalidArgumentException(sprintf(
			'Constant %s does not exist in class %s', $name, $this->reflection->getName()
		));
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
	 * @return ReflectionClass[]
	 */
	public function getParentClasses()
	{
		if ($this->parentClasses === NULL) {
			$this->parentClasses = array_map(function (IReflectionClass $class) {
				return $this->getParsedClasses()[$class->getName()];
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
			return $this->getParsedClasses()[$class->getName()];
		}, $this->reflection->getInterfaces());
	}


	/**
	 * @return array
	 */
	public function getOwnInterfaces()
	{
		return array_map(function (IReflectionClass $class) {
			return $this->getParsedClasses()[$class->getName()];
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
			if ( ! isset($this->getParsedClasses()[$class->getName()])) {
				return $class->getName();

			} else {
				return $this->getParsedClasses()[$class->getName()];
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
			if ( ! isset($this->getParsedClasses()[$class->getName()])) {
				return $class->getName();

			} else {
				return $this->getParsedClasses()[$class->getName()];
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
	 * @return ReflectionClass[]|array
	 */
	public function getDirectSubClasses()
	{
		$subClasses = [];
		foreach ($this->getParsedClasses() as $class) {
			if ($class->isDocumented() && $this->getName() === $class->getParentClassName()) {
				$subClasses[] = $class;
			}
		}
		uksort($subClasses, 'strcasecmp');
		return $subClasses;
	}


	/**
	 * @return ReflectionClass[]|array
	 */
	public function getIndirectSubClasses()
	{
		$subClasses = [];
		foreach ($this->getParsedClasses() as $class) {
			if ($class->isDocumented() && $this->getName() !== $class->getParentClassName()
				&& $class->isSubclassOf($this->getName())
			) {
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
	 * @param string $name
	 * @return bool
	 */
	public function hasProperty($name)
	{
		if ($this->properties === NULL) {
			$this->getProperties();
		}
		return isset($this->properties[$name]);
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasMethod($name)
	{
		return isset($this->getMethods()[$name]);
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
	public function isDocumented()
	{
		if ($this->isDocumented === NULL && parent::isDocumented()) {
			$fileName = $this->reflection->getFilename();
			$skipDocPath = $this->configuration->getOption(CO::SKIP_DOC_PATH);
			foreach ($skipDocPath as $mask) {
				if (fnmatch($mask, $fileName, FNM_NOESCAPE)) {
					$this->isDocumented = FALSE;
					break;
				}
			}
		}

		return $this->isDocumented;
	}


	/**
	 * @return int
	 */
	private function getVisibilityLevel()
	{
		return $this->configuration->getOption(CO::VISIBILITY_LEVELS);
	}


	/**
	 * @return bool
	 */
	private function isVisibilityLevelPublic()
	{
		return $this->getVisibilityLevel() & Visibility::IS_PUBLIC;
	}

}
