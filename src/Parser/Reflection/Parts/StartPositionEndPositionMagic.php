<?php declare(strict_types=1);

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
