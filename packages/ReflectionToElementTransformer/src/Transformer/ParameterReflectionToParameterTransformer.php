<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Parser\Reflection\ReflectionParameter;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use TokenReflection\IReflectionParameter;

final class ParameterReflectionToParameterTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionParameter;
    }

    /**
     * @param object|IReflectionParameter $reflection
     * @return ReflectionParameter
     */
    public function transform($reflection)
    {
        return new ReflectionParameter($reflection);
    }
}
