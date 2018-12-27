<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Method;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Reflection\AbstractParameterReflection;
use phpDocumentor\Reflection\DocBlock\Tags\Param;

/**
 * Class MethodParameterReflection
 * @package ApiGen\Reflection\Reflection\Method
 */
final class MethodParameterReflection extends AbstractParameterReflection implements MethodParameterReflectionInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->betterParameterReflection->getName();
    }

    /**
     * @return ClassMethodReflectionInterface
     * @throws \ApiGen\Reflection\Exception\UnsupportedReflectionClassException
     */
    public function getDeclaringFunction(): ClassMethodReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterParameterReflection->getDeclaringFunction()
        );
    }

    /**
     * @return string
     */
    public function getDeclaringFunctionName(): string
    {
        return $this->getDeclaringFunction()->getName();
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        $annotations = $this->getDeclaringFunction()->getAnnotation(AnnotationList::PARAM);

        if (empty($annotations[$this->betterParameterReflection->getPosition()])) {
            return '';
        }

        /** @var Param $paramAnnotation */
        $paramAnnotation = $annotations[$this->betterParameterReflection->getPosition()];

        return $paramAnnotation->getDescription()->render();
    }

    /**
     * @return string
     */
    public function getDeclaringClassName(): string
    {
        return $this->getDeclaringClass()->getName();
    }

    /**
     * @return ClassReflectionInterface
     */
    public function getDeclaringClass(): ClassReflectionInterface
    {
        return $this->getDeclaringFunction()->getDeclaringClass();
    }

    /**
     * @return ClassMethodReflectionInterface|TraitMethodReflectionInterface
     * @throws \ApiGen\Reflection\Exception\UnsupportedReflectionClassException
     */
    public function getDeclaringMethod()
    {
        return $this->transformerCollector->transformSingle(
            $this->betterParameterReflection->getDeclaringFunction()
        );
    }

    /**
     * @return string
     */
    public function getDeclaringMethodName(): string
    {
        return $this->getDeclaringMethod()->getName();
    }
}
