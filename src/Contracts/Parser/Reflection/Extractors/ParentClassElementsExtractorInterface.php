<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

interface ParentClassElementsExtractorInterface
{

    /**
     * @return ClassConstantReflectionInterface[]
     */
    public function getInheritedConstants();


    /**
     * @return PropertyReflectionInterface[][]
     */
    public function getInheritedProperties();


    /**
     * @return MethodReflectionInterface[]
     */
    public function getInheritedMethods();
}
