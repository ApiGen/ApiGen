<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InTraitInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\LinedInterface;

interface MethodReflectionInterface extends
    AbstractFunctionMethodReflectionInterface,
    InClassInterface,
    InTraitInterface,
    LinedInterface
{

    /**
     * @return bool
     */
    function isMagic();


    /**
     * @return bool
     */
    function isAbstract();


    /**
     * @return bool
     */
    function isFinal();


    /**
     * @return bool
     */
    function isStatic();


    /**
     * @return MethodReflectionInterface
     */
    function getImplementedMethod();


    /**
     * @return MethodReflectionInterface
     */
    function getOverriddenMethod();


    /**
     * @return MethodReflectionInterface
     */
    function getOriginal();


    /**
     * @return string
     */
    function getOriginalName();
}
