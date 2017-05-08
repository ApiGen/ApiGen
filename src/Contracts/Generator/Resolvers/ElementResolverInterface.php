<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator\Resolvers;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;

interface ElementResolverInterface
{
    public function getClass(string $className, string $namespace = ''): ?ClassReflectionInterface;

    /**
     * @return FunctionReflectionInterface|ClassMethodReflectionInterface|null
     */
    public function getFunction(string $name, string $namespace = '');

    /**
     * @param string|object $context
     * @return ReflectionInterface|bool
     */
    public function resolveElement(string $definition, $context, ?string &$expectedName = null);
}
