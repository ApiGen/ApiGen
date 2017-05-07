<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Extractors;

use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\MethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\PropertyReflectionInterface;

interface ParentClassElementsExtractorInterface
{
    /**
     * @return ClassConstantReflectionInterface[]
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
