<?php declare(strict_types=1);

namespace ApiGen\Element\Contract\ReflectionCollector;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;

interface BasicReflectionCollectorInterface
{
    public function processReflection(AbstractReflectionInterface $reflection): void;

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

    public function hasAnyElements(): bool;
}
