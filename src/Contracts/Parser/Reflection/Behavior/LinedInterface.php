<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

interface LinedInterface
{

    /**
     * @return int
     */
    function getStartLine();


    /**
     * @return int
     */
    function getEndLine();
}
