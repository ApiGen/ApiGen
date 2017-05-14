<?php declare(strict_types=1);

namespace ApiGen\Element\ReflectionCollector;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Element\Contract\ReflectionCollector\ReflectionCollectorInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;

abstract class AbstractReflectionCollector implements ReflectionCollectorInterface
{
    /**
     * @var mixed[]
     */
    protected $collectedReflections = [];

    public function hasAnyElements(): bool
    {
        return (bool) count($this->collectedReflections);
    }

    /**
     * @param object $reflection
     */
    protected function getReflectionInterfaceFromReflection($reflection): string
    {
        $implementedInterfaces = class_implements($reflection);

        return array_shift($implementedInterfaces);
    }
}
