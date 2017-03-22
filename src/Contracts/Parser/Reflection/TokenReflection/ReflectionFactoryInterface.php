<?php declare(strict_types=1);

namespace ApiGen\Contracts\Parser\Reflection\TokenReflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\Magic\MagicPropertyReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;

interface ReflectionFactoryInterface
{

    /**
     * @param object $tokenReflection
     * @return ClassReflectionInterface|ConstantReflectionInterface|MethodReflectionInterface
     */
    public function createFromReflection($tokenReflection);


    /**
     * @param mixed[] $settings
     * @return MagicMethodReflectionInterface
     */
    public function createMethodMagic(array $settings): MagicMethodReflectionInterface;


    /**
     * @param mixed[] $settings
     * @return MagicParameterReflectionInterface
     */
    public function createParameterMagic(array $settings): MagicParameterReflectionInterface;


    /**
     * @param mixed[] $settings
     * @return MagicPropertyReflectionInterface
     */
    public function createPropertyMagic(array $settings): MagicPropertyReflectionInterface;
}
