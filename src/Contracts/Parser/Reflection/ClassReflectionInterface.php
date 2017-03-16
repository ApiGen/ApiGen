<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ArrayObject;

interface ClassReflectionInterface extends ElementReflectionInterface, LinedInterface
{

    public function isDocumented(): bool;


    public function getParentClass(): ?ClassReflectionInterface;


    public function getParentClassName(): ?string;


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


    public function implementsInterface(string $name): bool;


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
    public function getInheritedMethods(): array;


    /**
     * @return array {[ className => MagicMethodReflectionInterface[] ]}
     */
    public function getInheritedMagicMethods(): array;


    /**
     * @return MethodReflectionInterface[]
     */
    public function getUsedMethods(): array;


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getUsedMagicMethods(): array;


    /**
     * @return MethodReflectionInterface[]
     */
    public function getTraitMethods(): array;


    /**
     * @return MagicMethodReflectionInterface[]
     */
    public function getOwnMagicMethods(): array;


    public function getMethod(string $name): MethodReflectionInterface;


    public function hasMethod(string $name): bool;


    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getConstants(): array;


    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getOwnConstants(): array;


    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array;


    public function hasConstant(string $name): bool;


    public function hasOwnConstant(string $name): bool;


    public function getConstant(string $name): ClassConstantReflectionInterface;


    public function getOwnConstant(string $name): ClassConstantReflectionInterface;


    public function getConstantReflection(string $name): ClassConstantReflectionInterface;


    public function getDocComment(): string;


    public function isVisibilityLevelPublic(): bool;


    public function getVisibilityLevel(): int;


    public function getReflectionFactory(): ReflectionFactoryInterface;


    /**
     * @return ClassReflectionInterface[]|string[]
     */
    public function getTraits(): array;


    /**
     * @return ClassReflectionInterface[]|string[]
     */
    public function getOwnTraits(): array;


    /**
     * @return string[]
     */
    public function getTraitNames(): array;


    /**
     * @return string[]
     */
    public function getOwnTraitNames(): array;


    /**
     * @return string[]
     */
    public function getTraitAliases(): array;


    /**
     * @return PropertyReflectionInterface[]
     */
    public function getProperties(): array;


    /**
     * @return PropertyReflectionInterface[]
     */
    public function getOwnProperties(): array;


    /**
     * @return array {[ className => PropertyReflectionInterface[] ]}
     */
    public function getInheritedProperties(): array;


    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getInheritedMagicProperties(): array;


    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getMagicProperties(): array;


    /**
     * @return MagicPropertyReflectionInterface[]
     */
    public function getOwnMagicProperties(): array;


    /**
     * @return PropertyReflectionInterface[]
     */
    public function getTraitProperties(): array;


    /**
     * @return array {[ traitName => PropertyReflectionInterface[] ]}
     */
    public function getUsedProperties(): array;


    /**
     * @return array {[ traitName => MagicPropertyReflectionInterface[] ]}
     */
    public function getUsedMagicProperties(): array;


    public function getProperty(string $name): PropertyReflectionInterface;


    public function hasProperty(string $name): bool;


    public function usesTrait(string $name): bool;


    /**
     * @return ClassReflectionInterface[]
     */
    public function getParsedClasses(): ArrayObject;


    public function getStartPosition(): int;


    public function getEndPosition(): int;


    public function isAbstract(): bool;


    public function isFinal(): bool;


    public function isInterface(): bool;


    public function isException(): bool;


    public function isTrait(): bool;


    public function isSubclassOf(string $class): bool;
}
