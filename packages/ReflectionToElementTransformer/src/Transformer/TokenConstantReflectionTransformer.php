<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use TokenReflection\IReflectionConstant;

final class TokenConstantReflectionTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionConstant && $reflection->getDeclaringClass();
    }

    /**
     * @param IReflectionConstant $reflection
     */
    public function transform($reflection): ReflectionConstant
    {
        return new ReflectionConstant($reflection);
    }
}
