<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection\Method;

use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;

interface MethodParameterReflectionInterface extends AbstractParameterReflectionInterface
{
    /**
     * @return ClassMethodReflectionInterface|TraitMethodReflectionInterface
     */
    public function getDeclaringMethod();
}
