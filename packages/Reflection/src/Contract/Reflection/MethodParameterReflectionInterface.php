<?php declare(strict_types=1);

namespace ApiGen\Reflection\Contract\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;

interface MethodParameterReflectionInterface extends AbstractParameterReflectionInterface
{
    public function getDeclaringMethod(): MethodReflectionInterface;

    public function getDeclaringClassName(): string;

    public function getDeclaringClass(): ClassReflectionInterface;
}
