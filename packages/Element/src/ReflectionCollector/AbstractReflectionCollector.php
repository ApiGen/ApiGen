<?php declare(strict_types=1);

namespace ApiGen\Element\ReflectionCollector;

use ApiGen\Element\Contract\ReflectionCollector\ReflectionCollectorInterface;

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
