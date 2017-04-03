<?php declare(strict_types=1);

namespace ApiGen\ReflectionToElementTransformer\Transformer;

use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\ElementReflection\Reflection\InterfaceReflection;
use ApiGen\ElementReflection\Reflection\NewClassReflection;
use ApiGen\ElementReflection\Reflection\NewFunctionReflection;
use ApiGen\ElementReflection\Reflection\NewParameterReflection;
use ApiGen\ElementReflection\Reflection\TraitReflection;
use ApiGen\ReflectionToElementTransformer\Contract\Transformer\TransformerInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionFunction as BetterReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class BetterClassReflectionTransformer implements TransformerInterface
{
    /**
     * @var DocBlockFactory
     */
    private $docBlockFactory;

    /**
     * @var BetterParameterReflectionTransformer
     */
    private $betterParameterReflectionToParameterTransformer;

    public function __construct(
        DocBlockFactory $docBlockFactory,
        BetterParameterReflectionTransformer $betterParameterReflectionToParameterTransformer
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
     * @return NewClassReflection|InterfaceReflection|TraitReflection
     */
    public function transform($reflection)
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() . ' ');
//        $parameters = $this->transformParameters($reflection);


        if ($reflection->isInterface()) {
            return $this->createInterface($reflection);
        }

        if ($reflection->isTrait()) {

        }

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

    private function createInterface(ReflectionClass $reflection): InterfaceReflection
    {
        $docBlock = $this->docBlockFactory->create($reflection->getDocComment() . ' ');
        return new InterfaceReflection($reflection, $docBlock);
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
