<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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
