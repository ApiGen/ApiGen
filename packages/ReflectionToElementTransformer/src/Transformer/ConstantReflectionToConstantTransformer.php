<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use TokenReflection\IReflectionConstant;

final class ConstantReflectionToConstantTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionConstant;
    }

    /**
     * @param object|IReflectionConstant $reflection
     * @return ReflectionConstant
     */
    public function transform($reflection)
    {
        return new ReflectionConstant($reflection);
    }
}
