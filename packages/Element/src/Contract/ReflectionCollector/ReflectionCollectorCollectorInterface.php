<?php declare(strict_types=1);

namespace ApiGen\Element\Contract\ReflectionCollector;

interface ReflectionCollectorCollectorInterface
{
    public function addReflectionCollector(ReflectionCollectorInterface $reflectionCollector): void;

    /**
     * @param object $reflection
     */
    public function processReflection($reflection): void;
}
