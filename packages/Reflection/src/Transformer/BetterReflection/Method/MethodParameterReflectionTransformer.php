<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Method;

use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Method\MethodParameterReflection;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class MethodParameterReflectionTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        if (! $reflection instanceof ReflectionParameter) {
            return false;
        }

        return (bool) $reflection->getDeclaringClass();
    }

    /**
     * @param ReflectionParameter|object $reflection
     */
    public function transform($reflection): MethodParameterReflection
    {
        return new MethodParameterReflection($reflection);
    }
}
