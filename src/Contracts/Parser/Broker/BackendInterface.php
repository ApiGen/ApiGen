<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Broker;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

interface BackendInterface
{

    /**
     * Returns all classes from all namespaces.
     *
     * @param int $type Returned class types (multiple values may be OR-ed).
     * @return ClassReflectionInterface[]
     */
    public function getClasses($type);


    /**
     * Returns all constants from all namespaces.
     *
     * @return ConstantReflectionInterface[]
     */
    public function getConstants();


    /**
     * Returns all functions from all namespaces.
     *
     * @return FunctionReflectionInterface[]
     */
    public function getFunctions();
}
