<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

interface InClassInterface extends InNamespaceInterface
{

    /**
     * @return ClassReflectionInterface
     */
    function getDeclaringClass();


    /**
     * @return string
     */
    function getPackageName();


    /**
     * @return string
     */
    function getNamespaceName();


    /**
     * @return array
     */
    function getAnnotations();


    /**
     * @return string[]
     */
    function getNamespaceAliases();
}
