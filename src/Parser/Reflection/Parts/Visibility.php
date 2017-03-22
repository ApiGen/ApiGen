<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Parts;

use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionProperty;

/**
 * @property-read IReflectionMethod|IReflectionProperty $reflection
 */
trait Visibility
{

    public function isPrivate(): bool
    {
        return $this->reflection->isPrivate();
    }


    public function isProtected(): bool
    {
        return $this->reflection->isProtected();
    }


    public function isPublic(): bool
    {
        return $this->reflection->isPublic();
    }
}
