<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Elements;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;

interface ElementSorterInterface
{

    /**
     * @param ElementReflectionInterface[]
     * @return ElementReflectionInterface[]
     */
    public function sortElementsByFqn(array $elements);
}
