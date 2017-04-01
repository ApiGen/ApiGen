<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Parser\Reflection\ReflectionMethod;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use TokenReflection\IReflectionMethod;

final class MethodReflectionToMethodTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionMethod;
    }

    /**
     * @param object|IReflectionMethod $reflection
     * @return ReflectionMethod
     */
    public function transform($reflection)
    {
        return new ReflectionMethod($reflection);
    }
}
