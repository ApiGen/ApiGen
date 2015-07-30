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
    public function isDocumented();


    /**
     * @return bool
     */
    public function isValid();


    /**
     * @return ClassReflectionInterface|NULL
     */
    public function getParentClass();


    /**
     * @return string
     */
    public function getParentClassName();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getParentClasses();


    /**
     * @return string[]
     */
    public function getParentClassNameList();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectSubClasses();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectSubClasses();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectImplementers();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementers();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectUsers();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectUsers();


    /**
     * @param string $name
     * @return bool
     */
    public function implementsInterface($name);


    /**
     * @return ClassReflectionInterface[]
     */
    public function getInterfaces();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getOwnInterfaces();


    /**
     * @return string[]
     */
    public function getOwnInterfaceNames();


    /**
     * @return MethodReflectionInterface[]
     */
    public function getMethods();


    /**
     * @return MethodReflectionInterface[]
     */
    public function getOwnMethods();


    /**
     * @return array {[ className => MethodReflectionInterface[] ]}
     */
    public function getInheritedMethods();


    /**
     * @return array {[ className => MagicMethodReflectionInterface[] ]}
     */
    public function getInheritedMagicMethods();


    /**
     * @return MethodReflectionInterface[]
     */
    public function getUsedMethods();


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getUsedMagicMethods();


    /**
     * @return MethodReflectionInterface[]
     */
    public function getTraitMethods();


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getOwnMagicMethods();


    /**
     * @param string $name
     * @return MethodReflectionInterface
     */
    public function getMethod($name);


    /**
     * @param string $name
     * @return bool
     */
    public function hasMethod($name);


    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getConstants();


    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getOwnConstants();


    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getInheritedConstants();


    /**
     * @param string $name
     * @return bool
     */
    public function hasConstant($name);


    /**
     * @param string $name
     * @return bool
     */
    public function hasOwnConstant($name);


    /**
     * @param string $name
     * @return ClassConstantReflectionInterface
     */
    public function getConstant($name);


    /**
     * @param string $name
     * @return bool
     */
    public function getOwnConstant($name);


    /**
     * @param string $name
     * @return ClassConstantReflectionInterface
     */
    public function getConstantReflection($name);


    /**
     * @return string
     */
    public function getDocComment();


    /**
     * @return bool
     */
    public function isVisibilityLevelPublic();


    /**
     * @return int
     */
    public function getVisibilityLevel();


    /**
     * @return ReflectionFactoryInterface
     */
    public function getReflectionFactory();


    /**
     * @return ClassReflectionInterface[]|string[]
     */
    public function getTraits();


    /**
     * @return ClassReflectionInterface[]|string[]
     */
    public function getOwnTraits();


    /**
     * @return string[]
     */
    public function getTraitNames();


    /**
     * @return string[]
     */
    public function getOwnTraitNames();


    /**
     * @return string[]
     */
    public function getTraitAliases();


    /**
     * @return PropertyReflectionInterface[]
     */
    public function getProperties();


    /**
     * @return PropertyReflectionInterface[]
     */
    public function getOwnProperties();


    /**
     * @return array {[ className => PropertyReflectionInterface[] ]}
     */
    public function getInheritedProperties();


    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getInheritedMagicProperties();


    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getMagicProperties();


    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getOwnMagicProperties();


    /**
     * @return PropertyReflectionInterface[]
     */
    public function getTraitProperties();


    /**
     * @return array {[ traitName => PropertyReflectionInterface[] ]}
     */
    public function getUsedProperties();


    /**
     * @return array {[ traitName => MagicPropertyReflectionInterface[] ]}
     */
    public function getUsedMagicProperties();


    /**
     * @param string $name
     * @return PropertyReflectionInterface
     */
    public function getProperty($name);


    /**
     * @param string $name
     * @return bool
     */
    public function hasProperty($name);


    /**
     * @param string $name
     * @return bool
     */
    public function usesTrait($name);


    /**
     * @return ClassReflectionInterface[]
     */
    public function getParsedClasses();


    /**
     * @return int
     */
    public function getStartPosition();


    /**
     * @return int
     */
    public function getEndPosition();


    /**
     * @return bool
     */
    public function isAbstract();


    /**
     * @return bool
     */
    public function isFinal();


    /**
     * @return bool
     */
    public function isInterface();


    /**
     * @return bool
     */
    public function isException();


    /**
     * @return bool
     */
    public function isTrait();


    /**
     * @param string $class
     * @return bool
     */
    public function isSubclassOf($class);
}
