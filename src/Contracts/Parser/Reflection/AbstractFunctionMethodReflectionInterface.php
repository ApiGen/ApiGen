<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection;

interface AbstractFunctionMethodReflectionInterface extends ElementReflectionInterface
{

    /**
     * @return bool
     */
    function returnsReference();


    /**
     * @return ParameterReflectionInterface[]
     */
    function getParameters();


    /**
     * @param int|string $key
     * @return ParameterReflectionInterface
     */
    function getParameter($key);
}
