<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\ElementReflection\Reflection\NewParameterReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class BetterParameterReflectionToParameterTransformer implements TransformerInterface
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
    public function transform($reflection): NewParameterReflection
    {
        return new NewParameterReflection($reflection);
    }
}
