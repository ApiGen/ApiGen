<?php

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

interface ClassTraitElementsExtractorInterface
{

    /**
     * @return ClassReflectionInterface[]
     */
    public function getDirectUsers();


    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectUsers();


    /**
     * @return PropertyReflectionInterface[]
     */
    public function getTraitProperties();


    /**
     * @return PropertyReflectionInterface[][]
     */
    public function getUsedProperties();


    /**
     * @return MethodReflectionInterface[]
     */
    public function getTraitMethods();


    /**
     * @return MethodReflectionInterface[]
     */
    public function getUsedMethods();
}
