<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\MethodReflectionInterface;

interface MethodParameterReflectionInterface extends AbstractParameterReflectionInterface
{
    public function getDeclaringMethod(): MethodReflectionInterface;

    public function getDeclaringClassName(): string;

    public function getDeclaringClass(): ClassReflectionInterface;
}
