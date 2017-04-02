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
     * @param IReflectionProperty $reflection
     */
    public function transform($reflection): ReflectionProperty
    {
        return new ReflectionProperty($reflection);
    }
}
