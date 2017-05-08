<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Extractors;

use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;

interface ParentClassElementsExtractorInterface
{
    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getInheritedConstants(): array;

    /**
     * @return ClassPropertyReflectionInterface[][]
     */
    public function getInheritedProperties(): array;

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getInheritedMethods(): array;
}
