<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;

interface TraitReflectionInterface extends AnnotationsInterface, AbstractReflectionInterface
{
    public function getShortName(): string;

    public function isDeprecated(): bool;

    public function getNamespaceName(): string;

    public function getFileName(): string;

    /**
     * @return ClassReflectionInterface[]|TraitReflectionInterface[]
     */
    public function getUsers(): array;

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getMethods(): array;

    /**
     * @return TraitMethodReflectionInterface[]
     */
    public function getOwnMethods(): array;

    public function getMethod(string $name): TraitMethodReflectionInterface;

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraits(): array;

    /**
     * @return string[]
     */
    public function getTraitAliases(): array;

    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getProperties(): array;

    /**
     * @return TraitPropertyReflectionInterface[]
     */
    public function getOwnProperties(): array;

    public function getProperty(string $name): TraitPropertyReflectionInterface;
}
