<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Parser\Reflection\ReflectionMethod;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use TokenReflection\IReflectionMethod;

final class TokenMethodReflectionTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionMethod;
    }

    /**
     * @param IReflectionMethod $reflection
     */
    public function transform($reflection): ReflectionMethod
    {
        return new ReflectionMethod($reflection);
    }
}
