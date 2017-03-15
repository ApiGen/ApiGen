<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection\Parts;

use TokenReflection\IReflectionMethod;
use TokenReflection\IReflectionProperty;

/**
 * @property-read IReflectionMethod|IReflectionProperty $reflection
 */
trait Visibility
{

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->reflection->isPrivate();
    }


    /**
     * @return bool
     */
    public function isProtected()
    {
        return $this->reflection->isProtected();
    }


    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->reflection->isPublic();
    }
}
