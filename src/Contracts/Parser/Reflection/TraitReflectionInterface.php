<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Reflection\Contract\TransformerCollectorInterface;

interface TraitReflectionInterface extends ReflectionInterface
{
    public function getPrettyName(): string;

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
     * @return MethodReflectionInterface[]
     */
    public function getMethods(): array;

    /**
     * @return MethodReflectionInterface[]
     */
    public function getOwnMethods(): array;

    /**
     * @return MethodReflectionInterface[]
     */
    public function getUsedMethods(): array;

    /**
     * @return MethodReflectionInterface[]
     */
    public function getTraitMethods(): array;

    public function getMethod(string $name): MethodReflectionInterface;

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
