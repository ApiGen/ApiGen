<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\ElementReflection\Reflection\NewClassReflection;
use ApiGen\ElementReflection\Reflection\NewFunctionReflection;
use ApiGen\ElementReflection\Reflection\NewParameterReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class BetterClassReflectionToClassTransformer implements TransformerInterface
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
        return $reflection instanceof ReflectionClass;
    }

    /**
     * @param ReflectionClass $reflection
     */
    public function transform($reflection): NewClassReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() . ' ');
//        $parameters = $this->transformParameters($reflection);

        $classReflection = new NewClassReflection(
            $reflection,
            $docBlock
//            $constants,
//            $properties,
//            $methods
        );

//        foreach ($parameters as $parameter) {
//            /** @var NewParameterReflection $parameter */
//            $parameter->setDeclaringFunction($functionReflection);
//        }
//
        return $classReflection;
    }

//    /**
//     * @return ParameterReflectionInterface[]
//     */
//    private function transformParameters(ReflectionFunction $reflection): array
//    {
//        return array_map(function (ReflectionParameter $parameterReflection) {
//            return $this->betterParameterReflectionToParameterTransformer->transform(
//                $parameterReflection
//            );
//        }, $reflection->getParameters());
//    }
}
