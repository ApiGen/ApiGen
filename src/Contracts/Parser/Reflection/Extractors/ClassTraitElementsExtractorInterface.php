<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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
