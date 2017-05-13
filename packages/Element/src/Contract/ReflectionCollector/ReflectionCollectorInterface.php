<?php declare(strict_types=1);

namespace ApiGen\Element\Contract\ReflectionCollector;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;

interface ReflectionCollectorInterface
{
    /**
     * @param object $reflection
     */
    public function processReflection($reflection): void;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassReflections(): array;

    /**
     * @return InterfaceReflectionInterface[]
     */
    public function getInterfaceReflections(): array;

    /**
     * @return TraitReflectionInterface[]
     */
    public function getTraitReflections(): array;

    /**
     * @return FunctionReflectionInterface[]
     */
    public function getFunctionReflections(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getClassOrTraitMethodReflections(): array;

    /**
     * @return ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[]
     */
    public function getClassOrTraitPropertyReflections(): array;
    /**
     * @return ClassReflectionInterface[]|InterfaceReflectionInterface[]
     */
    public function getClassOrInterfaceConstantReflections(): array;

    public function hasAnyElements(): bool;
}
