<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer\BetterReflection\Function_;

use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use ApiGen\Reflection\Reflection\Function_\FunctionParameterReflection;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class FunctionParameterReflectionTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        if (! $reflection instanceof ReflectionParameter) {
            return false;
        }

        return $reflection->getDeclaringClass() === null;
    }

    /**
     * @param object|ReflectionParameter $reflection
     */
    public function transform($reflection): FunctionParameterReflectionInterface
    {
        return new FunctionParameterReflection($reflection);
    }
}
