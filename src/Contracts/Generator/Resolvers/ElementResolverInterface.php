<?php

namespace ApiGen\Contracts\Generator\Resolvers;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;

interface ElementResolverInterface
{

    /**
     * @param string $className
     * @param string $namespace
     * @return ClassReflectionInterface|bool
     */
    public function getClass($className, $namespace = '');


    /**
     * @param string $className
     * @param string $namespace
     * @return ConstantReflectionInterface|bool
     */
    public function getConstant($className, $namespace = '');


    /**
     * @param string $name
     * @param string $namespace
     * @return FunctionReflectionInterface|NULL
     */
    public function getFunction($name, $namespace = '');


    /**
     * @param string $definition
     * @param string $context
     * @param string $expectedName
     * @return ElementReflectionInterface|bool
     */
    public function resolveElement($definition, $context, &$expectedName = null);
}
