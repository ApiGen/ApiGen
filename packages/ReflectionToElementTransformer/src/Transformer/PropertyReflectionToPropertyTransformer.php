<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Parser\Reflection\ReflectionProperty;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use TokenReflection\IReflectionProperty;

final class PropertyReflectionToPropertyTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionProperty;
    }

    /**
     * @param object|IReflectionProperty $reflection
     * @return ReflectionProperty
     */
    public function transform($reflection)
    {
        return new ReflectionProperty($reflection);
    }
}
