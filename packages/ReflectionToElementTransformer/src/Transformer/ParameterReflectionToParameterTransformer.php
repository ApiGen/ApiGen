<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\ElementReflection\Reflection\NewClassReflection;
use ApiGen\ElementReflection\Reflection\NewFunctionReflection;
use ApiGen\ElementReflection\Reflection\NewMethodReflection;
use ApiGen\ElementReflection\Reflection\NewParameterReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use ApiGen\ReflectionToElementTransformer\Legacy\BetterFunctionReflectionParser;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use TokenReflection\IReflectionParameter;

/**
 * @deprecated Remove after removing old Parser.
 */
final class ParameterReflectionToParameterTransformer implements TransformerInterface
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    public function __construct(
        DocBlockFactory $docBlockFactory
    ) {
        $this->docBlockFactory = $docBlockFactory;
    }

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof IReflectionParameter;
    }

    /**
     * @param IReflectionParameter $reflection
     */
    public function transform($reflection): NewParameterReflection
    {
        $betterFunctionReflection = BetterFunctionReflectionParser::parseByNameAndFile(
            $reflection->getDeclaringFunctionName(),
            $reflection->getDeclaringFunction()->getFileName()
        );

        $matchingParameterReflection = null;
        foreach ($betterFunctionReflection->getParameters() as $parameterReflection) {
            if ($parameterReflection->getName() === $reflection->getName()) {
                $matchingParameterReflection = $parameterReflection;
            }
        }

        $newFunctionReflection = $this->createBetterFunctionReflectionForParameter($betterFunctionReflection);
        $newParameterReflection = new NewParameterReflection($matchingParameterReflection);
        $newParameterReflection->setDeclaringFunction($newFunctionReflection);

        return $newParameterReflection;
    }

    /**
     * @param ReflectionFunction|ReflectionMethod $reflection
     * @return NewFunctionReflection|NewMethodReflection
     */
    private function createBetterFunctionReflectionForParameter($reflection)
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() . ' ');

        if ($reflection instanceof ReflectionFunction) {
            return new NewFunctionReflection(
                $reflection,
                $docBlock,
                []
            );
        }

        if ($reflection instanceof ReflectionMethod) {
            $methodReflection = new NewMethodReflection(
                $reflection,
                $docBlock,
                []
            );

            $newClassReflection = new NewClassReflection($reflection->getDeclaringClass());
            $methodReflection->setDeclaringClass($newClassReflection);
            return $methodReflection;
        }
    }
}
