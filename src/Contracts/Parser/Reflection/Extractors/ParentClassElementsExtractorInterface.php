<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Extractors;

use ApiGen\Contracts\Parser\Reflection\ClassConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

interface ParentClassElementsExtractorInterface
{

    /**
     * @return ClassConstantReflectionInterface[]
     */
    function getInheritedConstants();


    /**
     * @return PropertyReflectionInterface[][]
     */
    function getInheritedProperties();


    /**
     * @return MethodReflectionInterface[]
     */
    function getInheritedMethods();
}
