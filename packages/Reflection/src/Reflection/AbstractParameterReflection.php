<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Roave\BetterReflection\Reflection\ReflectionParameter;

abstract class AbstractParameterReflection implements AbstractParameterReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionParameter
     */
    protected $betterParameterReflection;

    /**
     * @var TransformerCollector
     */
    protected $transformerCollector;

    public function __construct(ReflectionParameter $betterParameterReflection)
    {
        $this->betterParameterReflection = $betterParameterReflection;
    }

    public function getTypeHint(): string
    {
        $types = (string) $this->betterParameterReflection->getType();
        $types = $this->removeClassPreSlashes($types);
        if ($types) {
            return $types;
        }

        $annotation = $this->getAnnotation();
        if ($annotation) {
            return (string) $annotation->getType();
        }

        return '';
    }

    public function isDefaultValueAvailable(): bool
    {
        return $this->betterParameterReflection->isDefaultValueAvailable();
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->betterParameterReflection->isDefaultValueAvailable()) {
            /* FIXME
            if ($this->betterParameterReflection->isDefaultValueConstant()) {
                return $this->betterParameterReflection->getDefaultValueConstantName();
            }
            */
            return $this->betterParameterReflection->getDefaultValue();
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

    public function isArray(): bool
    {
        return $this->betterParameterReflection->isArray();
    }

    public function isPassedByReference(): bool
    {
        return $this->betterParameterReflection->isPassedByReference();
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }

    private function removeClassPreSlashes(string $types): string
    {
        $typesInArray = explode('|', $types);
        array_walk($typesInArray, function (&$value): void {
            $value = ltrim($value, '\\');
        });

        return implode('|', $typesInArray);
    }

    private function getAnnotation(): ?Param
    {
        $declaringReflection = $this->getDeclaringReflection();
        $annotations = $declaringReflection->getAnnotation(AnnotationList::PARAM);

        if (empty($annotations[$this->betterParameterReflection->getPosition()])) {
            return null;
        }

        return $annotations[$this->betterParameterReflection->getPosition()];
    }

    /**
     * @return ClassMethodReflectionInterface|FunctionReflectionInterface|TraitMethodReflectionInterface
     */
    private function getDeclaringReflection()
    {
        if ($this instanceof FunctionParameterReflectionInterface) {
            return $this->getDeclaringFunction();
        }

        if ($this instanceof MethodParameterReflectionInterface) {
            return $this->getDeclaringMethod();
        }
    }
}
