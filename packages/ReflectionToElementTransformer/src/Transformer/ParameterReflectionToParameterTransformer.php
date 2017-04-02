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
     * @param IReflectionParameter $reflection
     */
    public function transform($reflection): ReflectionParameter
    {
        return new ReflectionParameter($reflection);
    }
}
