<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\ElementReflection\Reflection\NewFunctionReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use ApiGen\ReflectionToElementTransformer\Legacy\BetterFunctionReflectionParser;
use TokenReflection\IReflectionFunction;

/**
 * @deprecated Remove after removing old Parser.
 *
 * Will be replaced by @see BetterFunctionReflectionToFunctionTransformer
 */
final class FunctionReflectionToFunctionTransformer implements TransformerInterface
{
    /**
     * @var BetterFunctionReflectionToFunctionTransformer
     */
    private $betterFunctionReflectionToFunctionTransformer;

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionFunction;
    }

    public function __construct(BetterFunctionReflectionToFunctionTransformer $betterFunctionReflectionToFunctionTransformer)
    {
        $this->betterFunctionReflectionToFunctionTransformer = $betterFunctionReflectionToFunctionTransformer;
    }

    /**
     * @param IReflectionFunction $reflection
     */
    public function transform($reflection): NewFunctionReflection
    {
        $betterFunctionReflection = BetterFunctionReflectionParser::parseByNameAndFile($reflection->getName(), $reflection->getFileName());

        return $this->betterFunctionReflectionToFunctionTransformer->transform(
            $betterFunctionReflection
        );
    }
}
