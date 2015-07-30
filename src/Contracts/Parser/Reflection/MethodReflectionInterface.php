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
    public function isMagic();


    /**
     * @return bool
     */
    public function isAbstract();


    /**
     * @return bool
     */
    public function isFinal();


    /**
     * @return bool
     */
    public function isStatic();


    /**
     * @return MethodReflectionInterface
     */
    public function getImplementedMethod();


    /**
     * @return MethodReflectionInterface
     */
    public function getOverriddenMethod();


    /**
     * @return MethodReflectionInterface
     */
    public function getOriginal();


    /**
     * @return string
     */
    public function getOriginalName();
}
