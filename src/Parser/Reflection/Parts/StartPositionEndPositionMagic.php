<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Reflection\Parts;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

/**
 * @property-read ClassReflectionInterface $declaringClass
 */
trait StartPositionEndPositionMagic
{

    /**
     * @return int
     */
    public function getStartPosition()
    {
        return $this->declaringClass->getStartPosition();
    }


    /**
     * @return int
     */
    public function getEndPosition()
    {
        return $this->declaringClass->getEndPosition();
    }
}
