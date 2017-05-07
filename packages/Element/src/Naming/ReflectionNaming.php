<?php declare(strict_types=1);

namespace ApiGen\Element\Naming;

// removed from reflections, as suggested in issues
// use only for single method filter and AutocompleteGenerator

use ApiGen\Reflection\Contract\Reflection\MethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\PropertyReflectionInterface;

final class ReflectionNaming
{
    public function forMethodReflection(MethodReflectionInterface $methodReflection): string
    {
        return $methodReflection->getDeclaringClassName() . '::' . $methodReflection->getName() . '()';
    }

    public function forPropertyReflection(PropertyReflectionInterface $propertyReflection): string
    {
        return $propertyReflection->getDeclaringClassName() . '::$' . $propertyReflection->getName();
    }
}
