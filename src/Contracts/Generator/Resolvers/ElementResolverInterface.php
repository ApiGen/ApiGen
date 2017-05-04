<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator\Resolvers;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;

interface ElementResolverInterface
{
    public function getClass(string $className, string $namespace = ''): ?ClassReflectionInterface;

    /**
     * @return FunctionReflectionInterface|MethodReflectionInterface|null
     */
    public function getFunction(string $name, string $namespace = '');

    /**
     * @param string|object $context
     * @return ReflectionInterface|bool
     */
    public function resolveElement(string $definition, $context, ?string &$expectedName = null);
}
