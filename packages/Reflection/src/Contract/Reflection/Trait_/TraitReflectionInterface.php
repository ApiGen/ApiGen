<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;

interface TraitReflectionInterface extends ReflectionInterface
{
    public function isDocumented(): bool;

    public function getFileName(): string;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectUsers(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectUsers(): array;

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getMethods(): array;

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getOwnMethods(): array;

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getTraitMethods(): array;

    public function getMethod(string $name): ClassMethodReflectionInterface;

    public function hasMethod(string $name): bool;

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
    public function getTraitProperties(): array;

    /**
     * @return ClassPropertyReflectionInterface[]
     */
    public function getUsedProperties(): array;

    public function getProperty(string $name): TraitPropertyReflectionInterface;

    public function hasProperty(string $name): bool;

    public function usesTrait(string $name): bool;
}
