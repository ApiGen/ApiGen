<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\ElementReflection\Reflection\ClassReflection;
use ApiGen\ElementReflection\Reflection\FunctionReflection;
use ApiGen\ElementReflection\Reflection\ClassMethodReflection;
use ApiGen\ElementReflection\Reflection\MethodParameterReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use ApiGen\ReflectionToElementTransformer\Legacy\BetterFunctionReflectionParser;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use TokenReflection\IReflectionParameter;

/**
 * @deprecated Remove after removing old Parser.
 */
final class TokenParameterReflectionTransformer implements TransformerInterface
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
    public function transform($reflection): MethodParameterReflection
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
        $newParameterReflection = new MethodParameterReflection($matchingParameterReflection);
        $newParameterReflection->setDeclaringFunction($newFunctionReflection);

        return $newParameterReflection;
    }

    /**
     * @param ReflectionFunction|ReflectionMethod $reflection
     * @return FunctionReflection|ClassMethodReflection
     */
    private function createBetterFunctionReflectionForParameter($reflection)
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() . ' ');

        if ($reflection instanceof ReflectionFunction) {
            return new FunctionReflection(
                $reflection,
                $docBlock,
                []
            );
        }

        if ($reflection instanceof ReflectionMethod) {
            $methodReflection = new ClassMethodReflection(
                $reflection,
                $docBlock,
                []
            );

            $docBlock = $this->docBlockFactory->create($reflection->getDocComment() . ' ');

            $newClassReflection = new ClassReflection($reflection->getDeclaringClass(), $docBlock);
            $methodReflection->setDeclaringClass($newClassReflection);
            return $methodReflection;
        }
    }
}
