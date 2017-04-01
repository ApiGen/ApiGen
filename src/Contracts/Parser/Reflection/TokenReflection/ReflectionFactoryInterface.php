<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\TokenReflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;

interface ReflectionFactoryInterface
{
    /**
     * @param object $reflection
     * @return ClassReflectionInterface|ConstantReflectionInterface|MethodReflectionInterface
     */
    public function createFromReflection($reflection);
}
