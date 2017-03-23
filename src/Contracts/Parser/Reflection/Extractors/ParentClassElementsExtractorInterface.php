<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

interface ParentClassElementsExtractorInterface
{
    /**
     * @return ConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array;

    /**
     * @return PropertyReflectionInterface[][]
     */
    public function getInheritedProperties(): array;

    /**
     * @return MethodReflectionInterface[]
     */
    public function getInheritedMethods(): array;
}
