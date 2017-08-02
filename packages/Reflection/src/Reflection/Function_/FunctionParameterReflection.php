<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Function_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Reflection\AbstractParameterReflection;
use phpDocumentor\Reflection\DocBlock\Tags\Param;

final class FunctionParameterReflection extends AbstractParameterReflection implements FunctionParameterReflectionInterface
{
    public function getName(): string
    {
        return $this->betterParameterReflection->getName();
    }

    public function getDeclaringFunction(): FunctionReflectionInterface
    {
        /** @var FunctionReflectionInterface $declaringFunction */
        $declaringFunction = $this->transformerCollector->transformSingle(
            $this->betterParameterReflection->getDeclaringFunction()
        );

        return $declaringFunction;
    }

    public function getDeclaringFunctionName(): string
    {
        return $this->getDeclaringFunction()
            ->getName();
    }

    public function getDescription(): string
    {
        if ($this->getAnnotation() === null) {
            return '';
        }

        return $this->getAnnotation()
            ->getDescription()
            ->render();
    }

    private function getAnnotation(): ?Param
    {
        /** @var Param[] $functionParamAnnotations */
        $functionParamAnnotations = $this->getDeclaringFunction()
            ->getAnnotation(AnnotationList::PARAM);

        foreach ($functionParamAnnotations as $functionParamAnnotation) {
            if ($functionParamAnnotation->getVariableName() === $this->getName()) {
                return $functionParamAnnotation;
            }
        }

        return null;
    }
}
