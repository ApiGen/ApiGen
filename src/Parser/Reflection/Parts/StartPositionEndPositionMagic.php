<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Parts;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;

/**
 * @property-read ClassReflectionInterface $declaringClass
 */
trait StartPositionEndPositionMagic
{

    public function getStartPosition(): int
    {
        return $this->declaringClass->getStartPosition();
    }


    public function getEndPosition(): int
    {
        return $this->declaringClass->getEndPosition();
    }
}
