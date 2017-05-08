<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Class_;

use ApiGen\Reflection\Reflection\Class_\ClassPropertyReflection;
use ApiGen\Reflection\Reflection\Method\MethodParameterReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use Roave\BetterReflection\Reflection\ReflectionParameter;
use Roave\BetterReflection\Reflection\ReflectionProperty;

final class ClassPropertyReflectionTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        if (! $reflection instanceof ReflectionProperty) {
            return false;
        }

        $declaringClass = $reflection->getDeclaringClass();
        if ($declaringClass === null) {
            return false;
        }

        return ! $declaringClass->isTrait() && ! $declaringClass->isInterface();
    }

    /**
     * @param ReflectionParameter $reflection
     */
    public function transform($reflection): MethodParameterReflection
    {
        return new ClassPropertyReflection(
            $reflection
        );
    }
}
