<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\ElementReflection\Reflection\NewFunctionReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class BetterFunctionReflectionToFunctionTransformer implements TransformerInterface
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var BetterParameterReflectionToParameterTransformer
     */
    private $betterParameterReflectionToParameterTransformer;

    public function __construct(
        DocBlockFactory $docBlockFactory,
        BetterParameterReflectionToParameterTransformer $betterParameterReflectionToParameterTransformer
    ) {
        $this->docBlockFactory = $docBlockFactory;
        $this->betterParameterReflectionToParameterTransformer = $betterParameterReflectionToParameterTransformer;
    }

    /**
     * @param object $reflection
     */
    public function matches($reflection): bool
    {
        return $reflection instanceof BetterReflectionFunction;
    }

    /**
     * @param BetterReflectionFunction $reflection
     */
    public function transform($reflection): NewFunctionReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment());
        $parameters = $this->transformParameters($reflection);

        $functionReflection = new NewFunctionReflection(
            $reflection,
            $docBlock,
            $parameters
        );

        foreach ($parameters as $parameter) {
            $parameter->setDeclaringFunction($functionReflection);
        }

        return $functionReflection;
    }

    /**
     * @return ParameterReflectionInterface[]
     */
    private function transformParameters(ReflectionFunction $reflection): array
    {
        return array_map(function (ReflectionParameter $parameterReflection) {
            return $this->betterParameterReflectionToParameterTransformer->transform(
                $parameterReflection
            );
        }, $reflection->getParameters());
    }
}
