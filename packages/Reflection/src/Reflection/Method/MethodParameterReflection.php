<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Method;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class MethodParameterReflection implements MethodParameterReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionParameter
     */
    private $betterParameterReflection;

    /**
     * @var ClassMethodReflectionInterface|FunctionReflectionInterface
     */
    private $declaringFunction;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    public function __construct(ReflectionParameter $betterParameterReflection)
    {
        $this->betterParameterReflection = $betterParameterReflection;
    }

    public function getName(): string
    {
        return $this->betterParameterReflection->getName();
    }

    /**
     * @return ClassMethodReflectionInterface|FunctionReflectionInterface
     */
    public function getDeclaringFunction()
    {
        return $this->declaringFunction;
    }

    public function getDeclaringFunctionName(): string
    {
        return $this->declaringFunction->getName();
    }

    public function getTypeHint(): string
    {
        if ($this->isArray()) {
            return 'array';
        }

        if ($this->betterParameterReflection->isCallable()) {
            return 'callable';
        }

        $className = $this->getClassName();
        if ($className) {
            return $className;
        }

        $annotation = $this->getAnnotation();
        if ($annotation) {
            return (string) $annotation->getType();
        }

        return '';
    }

    public function getDescription(): string
    {
        $annotations = $this->declaringFunction->getAnnotation(AnnotationList::PARAM);
        if (empty($annotations[$this->betterParameterReflection->getPosition()])) {
            return '';
        }

        /** @var Param $paramAnnotation */
        $paramAnnotation = $annotations[$this->betterParameterReflection->getPosition()];

        return $paramAnnotation->getDescription()
            ->render();
    }

    public function getDefaultValueDefinition(): ?string
    {
        if ($this->betterParameterReflection->isDefaultValueAvailable()) {
            return $this->betterParameterReflection->getDefaultValueAsString();
        }

        return null;
    }

    public function isArray(): bool
    {
        return $this->betterParameterReflection->isArray();
    }

    public function getClass(): ?ClassReflectionInterface
    {
        $typeHint = $this->betterParameterReflection->getTypeHint();
        if ($typeHint) {
            // @todo
        }

        return null;
    }

    public function getClassName(): ?string
    {
        $class = $this->getClass();
        if ($class) {
            return $class->getName();
        }

        return null;
    }

    public function getDeclaringClassName(): string
    {
        $declaringClass = $this->getDeclaringClass();
        if ($declaringClass) {
            return $declaringClass->getName();
        }

        return '';
    }

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        if ($this->declaringFunction instanceof ClassMethodReflectionInterface) {
            return $this->declaringFunction->getDeclaringClass();
        }

        return null;
    }

    public function isVariadic(): bool
    {
        return $this->betterParameterReflection->isVariadic();
    }

    public function isCallable(): bool
    {
        return $this->betterParameterReflection->isCallable();
    }

    /**
     * @param ClassMethodReflectionInterface|FunctionReflectionInterface $declaringFunction
     */
    public function setDeclaringFunction($declaringFunction): void
    {
        $this->declaringFunction = $declaringFunction;
    }

    private function getAnnotation(): ?Param
    {
        $annotations = $this->declaringFunction->getAnnotation(AnnotationList::PARAM);
        if (empty($annotations[$this->betterParameterReflection->getPosition()])) {
            return null;
        }

        return $annotations[$this->betterParameterReflection->getPosition()];
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

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
