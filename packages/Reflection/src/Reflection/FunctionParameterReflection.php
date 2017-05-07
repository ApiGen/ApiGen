<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionParameterReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class FunctionParameterReflection implements FunctionParameterReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionParameter
     */
    private $betterReflectionParameter;

    /**
     * @var FunctionReflectionInterface
     */
    private $declaringFunction;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    public function __construct(ReflectionParameter $betterParameterReflection)
    {
        $this->betterReflectionParameter = $betterParameterReflection;
    }

    public function getName(): string
    {
        return $this->betterReflectionParameter->getName();
    }

    public function getDeclaringFunction(): FunctionReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterReflectionParameter->getDeclaringFunction()
        );
    }

    public function getDeclaringFunctionName(): string
    {
        return $this->getDeclaringFunction()
            ->getName();
    }

    public function getTypeHint(): string
    {
        if ($this->betterReflectionParameter->isArray()) {
            return 'array';
        }

        if ($this->betterReflectionParameter->isCallable()) {
            return 'callable';
        }

        $className = $this->getClassName();
        if ($className) {
            return $className;
        }

        if (count($this->betterReflectionParameter->getDocBlockTypes())) {
            return implode('|', $this->betterReflectionParameter->getDocBlockTypeStrings());
        }

        return '';
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

    public function getDefaultValueDefinition(): ?string
    {
        if ($this->betterReflectionParameter->isDefaultValueAvailable()) {
            return $this->betterReflectionParameter->getDefaultValueAsString();
        }

        return null;
    }

    public function isArray(): bool
    {
        return $this->betterReflectionParameter->isArray();
    }

    public function getClass(): ?ClassReflectionInterface
    {
        $typeHint = $this->betterReflectionParameter->getTypeHint();
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

    public function isVariadic(): bool
    {
        return $this->betterReflectionParameter->isVariadic();
    }

    public function isCallable(): bool
    {
        return $this->betterReflectionParameter->isCallable();
    }

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
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
