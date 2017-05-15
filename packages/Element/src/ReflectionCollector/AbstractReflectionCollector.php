<?php declare(strict_types=1);

namespace ApiGen\Element\ReflectionCollector;

use ApiGen\Element\Contract\ReflectionCollector\ReflectionCollectorInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

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

}
