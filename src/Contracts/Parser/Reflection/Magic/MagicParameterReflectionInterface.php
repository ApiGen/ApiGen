<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Magic;

use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;

interface MagicParameterReflectionInterface extends ParameterReflectionInterface
{

    /**
     * @return string
     */
    public function getDocComment();


    /**
     * @return int
     */
    public function getStartLine();


    /**
     * @return int
     */
    public function getEndLine();


    /**
     * @return string
     */
    public function getFileName();


    /**
     * @return bool
     */
    public function isTokenized();
}
