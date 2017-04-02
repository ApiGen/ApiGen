<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\ElementReflection\Reflection\NewFunctionReflection;
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
     * @return FunctionReflectionInterface
     */
    public function transform($reflection)
    {
        return new NewFunctionReflection(
            $reflection
        );
    }
}
