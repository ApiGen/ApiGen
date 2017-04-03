<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\ElementReflection\Reflection\NewFunctionReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use TokenReflection\IReflectionFunction;

/**
 * @deprecated Remove after removing old Parser, Broker and Backend.
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
        $singleFileSourceLocator = new SingleFileSourceLocator($reflection->getFileName());
        $functionReflector = new FunctionReflector($singleFileSourceLocator);
        $functionReflections = $functionReflector->getAllFunctions();

        $specificFunctionReflection = null;
        foreach ($functionReflections as $functionReflection) {
            if ($functionReflection->getName() === $reflection->getName()) {
                $specificFunctionReflection = $functionReflection;
            }
        }

        return $this->betterFunctionReflectionToFunctionTransformer->transform(
            $specificFunctionReflection
        );
    }
}
