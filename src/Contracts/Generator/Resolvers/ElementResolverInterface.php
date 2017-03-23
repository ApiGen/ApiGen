<?php declare(strict_types=1);

namespace ApiGen\Contracts\Generator\Resolvers;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;

interface ElementResolverInterface
{
    public function getClass(string $className, string $namespace = ''): ?ClassReflectionInterface;

    public function getConstant(string $className, string $namespace = ''): ?ConstantReflectionInterface;

    /**
     * @return FunctionReflectionInterface|MethodReflectionInterface|null
     */
    public function getFunction(string $name, string $namespace = '');

    /**
     * @param string $definition
     * @param string|object $context
     * @param string $expectedName
     * @return ElementReflectionInterface|bool
     */
    public function resolveElement(string $definition, $context, ?string &$expectedName = null);
}
