<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\ElementReflection\Reflection\ParameterReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class BetterParameterReflectionTransformer implements TransformerInterface
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
    public function transform($reflection): ParameterReflection
    {
        return new ParameterReflection($reflection);
    }
}
