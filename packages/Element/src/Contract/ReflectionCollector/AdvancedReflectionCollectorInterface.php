<?php declare(strict_types=1);

namespace ApiGen\Element\Contract\ReflectionCollector;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;

interface AdvancedReflectionCollectorInterface extends BasicReflectionCollectorInterface
{
    /**
     * @return ClassMethodReflectionInterface[]|TraitMethodReflectionInterface[]
     */
    public function getClassOrTraitMethodReflections(string $filter): array;

    /**
     * @return ClassPropertyReflectionInterface[]|TraitPropertyReflectionInterface[]
     */
    public function getClassOrTraitPropertyReflections(string $filter): array;

    /**
     * @return ClassConstantReflectionInterface[]|InterfaceConstantReflectionInterface[]
     */
    public function getClassOrInterfaceConstantReflections(string $filter): array;
}
