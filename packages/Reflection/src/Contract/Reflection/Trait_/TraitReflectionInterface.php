<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\InNamespaceInterface;

interface TraitReflectionInterface extends AnnotationsInterface, AbstractReflectionInterface, InNamespaceInterface
{
    public function getShortName(): string;

    public function isDeprecated(): bool;

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
