<?php declare(strict_types=1);

namespace ApiGen\Element\Contract\ReflectionCollector;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;

interface AdvancedReflectionCollectorInterface extends BasicReflectionCollectorInterface
{
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
}
