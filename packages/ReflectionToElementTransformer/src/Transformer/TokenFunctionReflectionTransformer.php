<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\ElementReflection\Reflection\NewFunctionReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use ApiGen\ReflectionToElementTransformer\Legacy\BetterFunctionReflectionParser;
use TokenReflection\IReflectionFunction;

/**
 * @deprecated Remove after removing old Parser.
 *
 * Will be replaced by @see BetterFunctionReflectionTransformer
 */
final class TokenFunctionReflectionTransformer implements TransformerInterface
{
    /**
     * @var BetterFunctionReflectionTransformer
     */
    private $betterFunctionReflectionToFunctionTransformer;

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionFunction;
    }

    public function __construct(BetterFunctionReflectionTransformer $betterFunctionReflectionToFunctionTransformer)
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
