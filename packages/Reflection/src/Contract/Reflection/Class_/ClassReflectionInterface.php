<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;

interface ClassReflectionInterface extends StartAndEndLineInterface
{
    public function getName(): string;

    public function isDocumented(): bool;

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
     * @return ClassReflectionInterface[]
     */
    public function getInterfaces(): array;

    /**
     * @return ClassReflectionInterface[]
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
     * @return ClassMethodReflectionInterface[]
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

    public function getTransformerCollector(): TransformerCollectorInterface;

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
    public function getOwnTraitNames(): array;

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
}
