<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use Roave\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;

final class BetterFunctionReflectionToFunctionTransformer implements TransformerInterface
{
    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof BetterReflectionFunction;
    }

    /**
     * @param object|BetterReflectionFunction $reflection
     * @return ReflectionClass
     */
    public function transform($reflection)
    {
        dump($reflection);
        die;
        return new ReflectionFunction($reflection);
    }
}
