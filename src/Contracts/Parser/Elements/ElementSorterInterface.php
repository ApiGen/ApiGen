<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

use ApiGen\Contracts\Parser\Reflection\ReflectionInterface;

interface ElementSorterInterface
{
    /**
     * @param ReflectionInterface[] $elements
     * @return ReflectionInterface[]
     */
    public function sortElementsByFqn(array $elements): array;
}
