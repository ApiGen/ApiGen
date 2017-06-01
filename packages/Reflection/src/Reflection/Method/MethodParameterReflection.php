<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Method;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Reflection\AbstractParameterReflection;
use phpDocumentor\Reflection\DocBlock\Tags\Param;

final class MethodParameterReflection extends AbstractParameterReflection implements MethodParameterReflectionInterface
{
    public function getName(): string
    {
        return $this->betterParameterReflection->getName();
    }

    public function getDeclaringFunction(): ClassMethodReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterParameterReflection->getDeclaringFunction()
        );
    }

    public function getDeclaringFunctionName(): string
    {
        return $this->getDeclaringFunction()
            ->getName();
    }

    public function getDescription(): string
    {
        $annotations = $this->getDeclaringFunction()
            ->getAnnotation(AnnotationList::PARAM);

        if (empty($annotations[$this->betterParameterReflection->getPosition()])) {
            return '';
        }

        /** @var Param $paramAnnotation */
        $paramAnnotation = $annotations[$this->betterParameterReflection->getPosition()];

        return $paramAnnotation->getDescription()
            ->render();
    }

    public function getDeclaringClassName(): string
    {
        return $this->getDeclaringClass()
            ->getName();
    }

    public function getDeclaringClass(): ClassReflectionInterface
    {
        return $this->getDeclaringFunction()
            ->getDeclaringClass();
    }

    /**
     * @return ClassMethodReflectionInterface|TraitMethodReflectionInterface
     */
    public function getDeclaringMethod()
    {
        return $this->transformerCollector->transformSingle(
            $this->betterParameterReflection->getDeclaringFunction()
        );
    }

    public function getDeclaringMethodName(): string
    {
        return $this->getDeclaringMethod()
            ->getName();
    }
}
