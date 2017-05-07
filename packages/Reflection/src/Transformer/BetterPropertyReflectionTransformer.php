<?php declare(strict_types=1);

namespace ApiGen\Reflection\Transformer;

use ApiGen\Reflection\Reflection\MethodParameterReflection;
use ApiGen\Reflection\Contract\Transformer\TransformerInterface;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class BetterPropertyReflectionTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof ReflectionParameter;
    }

    /**
     * @param ReflectionParameter $reflection
     */
    public function transform($reflection): MethodParameterReflection
    {
        return new MethodParameterReflection($reflection);
    }
}