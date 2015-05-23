<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;


interface ClassReflectionInterface extends ElementReflectionInterface, LinedInterface
{

	/**
	 * @return bool
	 */
	function isDocumented();


	/**
	 * @return bool
	 */
	function isValid();


	/**
	 * @return ClassReflectionInterface|NULL
	 */
	function getParentClass();


	/**
	 * @return string
	 */
	function getParentClassName();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getParentClasses();


	/**
	 * @return string[]
	 */
	function getParentClassNameList();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getDirectSubClasses();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getIndirectSubClasses();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getDirectImplementers();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getIndirectImplementers();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getDirectUsers();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getIndirectUsers();


	/**
	 * @param string $name
	 * @return bool
	 */
	function implementsInterface($name);


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getInterfaces();


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getOwnInterfaces();


	/**
	 * @return string[]
	 */
	function getOwnInterfaceNames();


	/**
	 * @return MethodReflectionInterface[]
	 */
	function getMethods();


	/**
	 * @return MethodReflectionInterface[]
	 */
	function getOwnMethods();


	/**
	 * @return array {[ className => MethodReflectionInterface[] ]}
	 */
	function getInheritedMethods();


	/**
	 * @return array {[ className => MagicMethodReflectionInterface[] ]}
	 */
	function getInheritedMagicMethods();


	/**
	 * @return MethodReflectionInterface[]
	 */
	function getUsedMethods();


	/**
	 * @return MagicMethodReflectionInterface[]
	 */
	function getUsedMagicMethods();


	/**
	 * @return MethodReflectionInterface[]
	 */
	function getTraitMethods();


	/**
	 * @return MagicMethodReflectionInterface[]
	 */
	function getOwnMagicMethods();


	/**
	 * @param string $name
	 * @return MethodReflectionInterface
	 */
	function getMethod($name);


	/**
	 * @param string $name
	 * @return bool
	 */
	function hasMethod($name);


	/**
	 * @return ClassConstantReflectionInterface[]
	 */
	function getConstants();


	/**
	 * @return ClassConstantReflectionInterface[]
	 */
	function getOwnConstants();


	/**
	 * @return ClassConstantReflectionInterface[]
	 */
	function getInheritedConstants();


	/**
	 * @param string $name
	 * @return bool
	 */
	function hasConstant($name);


	/**
	 * @param string $name
	 * @return bool
	 */
	function hasOwnConstant($name);


	/**
	 * @param string $name
	 * @return ClassConstantReflectionInterface
	 */
	function getConstant($name);


	/**
	 * @param string $name
	 * @return bool
	 */
	function getOwnConstant($name);


	/**
	 * @param string $name
	 * @return ClassConstantReflectionInterface
	 */
	function getConstantReflection($name);


	/**
	 * @return string
	 */
	function getDocComment();


	/**
	 * @return bool
	 */
	function isVisibilityLevelPublic();


	/**
	 * @return int
	 */
	function getVisibilityLevel();


	/**
	 * @return ReflectionFactoryInterface
	 */
	function getReflectionFactory();


	/**
	 * @return ClassReflectionInterface[]|string[]
	 */
	function getTraits();


	/**
	 * @return ClassReflectionInterface[]|string[]
	 */
	function getOwnTraits();


	/**
	 * @return string[]
	 */
	function getTraitNames();


	/**
	 * @return string[]
	 */
	function getOwnTraitNames();


	/**
	 * @return string[]
	 */
	function getTraitAliases();


	/**
	 * @return PropertyReflectionInterface[]
	 */
	function getProperties();


	/**
	 * @return PropertyReflectionInterface[]
	 */
	function getOwnProperties();


	/**
	 * @return array {[ className => PropertyReflectionInterface[] ]}
	 */
	function getInheritedProperties();


	/**
	 * @return MagicPropertyReflectionInterface[]
	 */
	function getInheritedMagicProperties();


	/**
	 * @return MagicPropertyReflectionInterface[]
	 */
	function getMagicProperties();


	/**
	 * @return MagicPropertyReflectionInterface[]
	 */
	function getOwnMagicProperties();


	/**
	 * @return PropertyReflectionInterface[]
	 */
	function getTraitProperties();


	/**
	 * @return array {[ traitName => PropertyReflectionInterface[] ]}
	 */
	function getUsedProperties();


	/**
	 * @return array {[ traitName => MagicPropertyReflectionInterface[] ]}
	 */
	function getUsedMagicProperties();


	/**
	 * @param string $name
	 * @return PropertyReflectionInterface
	 */
	function getProperty($name);


	/**
	 * @param string $name
	 * @return bool
	 */
	function hasProperty($name);


	/**
	 * @param string $name
	 * @return bool
	 */
	function usesTrait($name);


	/**
	 * @return ClassReflectionInterface[]
	 */
	function getParsedClasses();


	/**
	 * @return int
	 */
	function getStartPosition();


	/**
	 * @return int
	 */
	function getEndPosition();


	/**
	 * @return bool
	 */
	function isAbstract();


	/**
	 * @return bool
	 */
	function isFinal();


	/**
	 * @return bool
	 */
	function isInterface();


	/**
	 * @return bool
	 */
	function isException();


	/**
	 * @return bool
	 */
	function isTrait();


	/**
	 * @param string $class
	 * @return bool
	 */
	function isSubclassOf($class);

}
