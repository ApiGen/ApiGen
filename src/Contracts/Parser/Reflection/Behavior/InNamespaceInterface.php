<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

interface InNamespaceInterface
{

    /**
     * @deprecated To be removed with ApiGen\ElementParser
     * @return string
     */
    function getDeclaringClassName();


    /**
     * @return string[]
     */
    function getNamespaceAliases();
}
