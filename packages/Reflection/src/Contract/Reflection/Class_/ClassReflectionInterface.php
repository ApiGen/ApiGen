<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FileNameAwareReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\InNamespaceInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\StartAndEndLineInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;

interface ClassReflectionInterface extends StartAndEndLineInterface, AnnotationsInterface, AbstractReflectionInterface, InNamespaceInterface, FileNameAwareReflectionInterface
{
    public function getParentClass(): ?self;

    public function getParentClassName(): ?string;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getParentClasses(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getSubClasses(): array;

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
     * @return TraitMethodReflectionInterface[]
     */
    public function getTraitMethods(): array;

    public function getMethod(string $name): ClassMethodReflectionInterface;

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

    public function getProperty(string $name): ClassPropertyReflectionInterface;

    public function isAbstract(): bool;

    public function isFinal(): bool;

    public function isSubclassOf(string $class): bool;

    public function getShortName(): string;

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getConstants(): array;

    public function isDeprecated(): bool;
}
