<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;

interface MethodParameterReflectionInterface extends AbstractParameterReflectionInterface
{
    public function getDeclaringMethod(): ClassMethodReflectionInterface;

    public function getDeclaringClassName(): string;

    public function getDeclaringClass(): ClassReflectionInterface;
}
