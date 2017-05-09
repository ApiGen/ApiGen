<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;

interface ClassReflectionInterface extends StartAndEndLineInterface, AnnotationsInterface
{
    public function getName(): string;

    public function getParentClass(): ?ClassReflectionInterface;

    public function getParentClassName(): ?string;

    public function getFileName(): string;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParentClasses(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectSubClasses(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectSubClasses(): array;

    public function implementsInterface(string $name): bool;

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaces(): array;

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getOwnInterfaces(): array;

    /**
     * @return string[]
     */
    public function getOwnInterfaceNames(): array;

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getMethods(): array;

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getOwnMethods(): array;

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getInheritedMethods(): array;

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getUsedMethods(): array;

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getTraitMethods(): array;

    public function getMethod(string $name): ClassMethodReflectionInterface;

    public function hasMethod(string $name): bool;

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getOwnConstants(): array;

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array;

    public function hasConstant(string $name): bool;

    public function getConstant(string $name): ClassConstantReflectionInterface;

    public function getOwnConstant(string $name): ClassConstantReflectionInterface;

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraits(): array;

    /**
     * @return TraitReflectionInterface[]
     */
    public function getOwnTraits(): array;

    /**
     * @return string[]
     */
    public function getTraitNames(): array;

    /**
     * @return string[]
     */
    public function getTraitAliases(): array;

    /**
     * @return ClassPropertyReflectionInterface[]
     */
    public function getProperties(): array;

    /**
     * @return ClassPropertyReflectionInterface[]
     */
    public function getOwnProperties(): array;

    /**
     * @return ClassPropertyReflectionInterface[]
     */
    public function getInheritedProperties(): array;

    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getTraitProperties(): array;

    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getUsedProperties(): array;

    public function getProperty(string $name): ClassPropertyReflectionInterface;

    public function hasProperty(string $name): bool;

    public function usesTrait(string $name): bool;

    public function isAbstract(): bool;

    public function isFinal(): bool;

    public function isSubclassOf(string $class): bool;

    public function getShortName(): string;

    public function getNamespaceName(): string;

    public function getConstants(): array;

    public function isDeprecated(): bool;
}
