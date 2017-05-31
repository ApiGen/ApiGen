<?php declare(strict_types=1);

namespace ApiGen\Element\ReflectionCollector;

use ApiGen\Element\Contract\ReflectionCollector\ReflectionCollectorCollectorInterface;
use ApiGen\Element\Contract\ReflectionCollector\ReflectionCollectorInterface;
use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

final class ReflectionCollectorCollector implements ReflectionCollectorCollectorInterface
{
    /**
     * @var ReflectionCollectorInterface[]
     */
    private $reflectionCollectors;

    public function addReflectionCollector(ReflectionCollectorInterface $reflectionCollector): void
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
