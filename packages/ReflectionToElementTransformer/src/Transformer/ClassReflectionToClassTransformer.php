<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use TokenReflection\IReflectionClass;

final class ClassReflectionToClassTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionClass;
    }

    /**
     * @param object $reflection
     * @return ReflectionClass
     */
    public function transform($reflection)
    {
        return new ReflectionClass($reflection);
    }
}
