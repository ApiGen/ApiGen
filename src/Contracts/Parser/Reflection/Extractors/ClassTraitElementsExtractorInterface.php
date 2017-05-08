<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Extractors;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\PropertyReflectionInterface;

interface ClassTraitElementsExtractorInterface
{
    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectUsers(): array;

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectUsers(): array;

    /**
     * @return PropertyReflectionInterface[]
     */
    public function getTraitProperties(): array;

    /**
     * @return PropertyReflectionInterface[][]
     */
    public function getUsedProperties(): array;

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getTraitMethods(): array;

    /**
     * @return ClassMethodReflectionInterface[]
     */
    public function getUsedMethods(): array;
}
