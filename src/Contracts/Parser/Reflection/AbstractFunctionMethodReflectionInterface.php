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
    public function returnsReference();


    /**
     * @return ParameterReflectionInterface[]
     */
    public function getParameters();


    /**
     * @param int|string $key
     * @return ParameterReflectionInterface
     */
    public function getParameter($key);
}
