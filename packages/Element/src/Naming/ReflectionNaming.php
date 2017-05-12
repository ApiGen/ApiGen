<?php declare(strict_types=1);

namespace ApiGen\Element\Naming;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;

final class ReflectionNaming
{
    public function forMethodReflection(ClassMethodReflectionInterface $methodReflection): string
    {
        return $methodReflection->getDeclaringClassName() . '::' . $methodReflection->getName() . '()';
    }
}
