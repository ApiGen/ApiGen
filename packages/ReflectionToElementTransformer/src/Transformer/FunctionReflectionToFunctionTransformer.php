<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use TokenReflection\IReflectionFunction;

final class FunctionReflectionToFunctionTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionFunction;
    }

    /**
     * @param object|IReflectionFunction $reflection
     */
    public function transform($reflection): ReflectionFunction
    {
        return new ReflectionFunction($reflection);
    }
}
