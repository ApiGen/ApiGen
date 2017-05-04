<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

use ApiGen\Contracts\Parser\Reflection\ReflectionInterface;

interface ElementFilterInterface
{
    /**
     * @param ReflectionInterface[] $elements
     * @return ReflectionInterface[]
     */
    public function filterByAnnotation(array $elements, string $annotation): array;
}
