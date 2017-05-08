<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

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
    public function getUsedMethods(): array;

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
     * @return PropertyReflectionInterface[]
     */
    public function getProperties(): array;

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getOwnProperties(): array;

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getTraitProperties(): array;

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getUsedProperties(): array;

    public function getProperty(string $name): PropertyReflectionInterface;

    public function hasProperty(string $name): bool;

    public function usesTrait(string $name): bool;

    public function isAbstract(): bool;

    public function isFinal(): bool;
}
