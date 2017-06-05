<?php declare(strict_types=1);

namespace ApiGen\Element\Contract\ReflectionCollector;

use ApiGen\Reflection\Contract\Reflection\AbstractReflectionInterface;

interface ReflectionCollectorCollectorInterface
{
    public function addReflectionCollector(AdvancedReflectionCollectorInterface $reflectionCollector): void;

    public function processReflection(AbstractReflectionInterface $reflection): void;
}
