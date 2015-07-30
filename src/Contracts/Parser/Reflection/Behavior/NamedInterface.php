<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Contracts\Parser\Reflection\Behavior;

interface NamedInterface
{

    /**
     * @return string
     */
    public function getName();


    /**
     * @return string
     * @todo dunno if this belongs here
     */
    public function getPrettyName();
}
