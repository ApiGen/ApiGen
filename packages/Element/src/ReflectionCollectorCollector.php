<?php declare(strict_types=1);

namespace ApiGen\Element;

use ApiGen\Element\Contract\ReflectionCollector\AdvancedReflectionCollectorInterface;
use ApiGen\Element\Contract\ReflectionCollector\BasicReflectionCollectorInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

final class ReflectionCollectorCollector
{
    /**
     * @var BasicReflectionCollectorInterface[]|AdvancedReflectionCollectorInterface[]
     */
    private $reflectionCollectors = [];

    public function addReflectionCollector(BasicReflectionCollectorInterface $reflectionCollector): void
    {
        $this->reflectionCollectors[] = $reflectionCollector;
    }

    public function processReflection(AbstractReflectionInterface $reflection): void
    {
        foreach ($this->reflectionCollectors as $reflectionCollector) {
            $reflectionCollector->processReflection($reflection);
        }
    }
}
