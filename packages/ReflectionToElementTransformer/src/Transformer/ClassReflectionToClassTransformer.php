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
     * @param IReflectionClass $reflection
     */
    public function transform($reflection): ReflectionClass
    {
        return new ReflectionClass($reflection);
    }
}
