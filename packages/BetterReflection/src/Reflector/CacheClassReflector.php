<?php declare(strict_types=1);

namespace ApiGen\BetterReflection\Reflector;

use Roave\BetterReflection\Reflection\Reflection;
use Roave\BetterReflection\Reflector\ClassReflector;

class CacheClassReflector extends ClassReflector
{
    private $reflectCache = [];

    /**
     * @inheritDoc
     */
    public function reflect(string $className): Reflection
    {
        if (isset($this->reflectCache[$className])) {
            return $this->reflectCache[$className];
        }
        $class = parent::reflect($className);
        $this->reflectCache[$className] = $class;

        return $class;
    }
}
