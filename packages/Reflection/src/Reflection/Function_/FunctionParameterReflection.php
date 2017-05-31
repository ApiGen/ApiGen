<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Function_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Types\Object_;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class FunctionParameterReflection implements FunctionParameterReflectionInterface,
    TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionParameter
     */
    private $betterParameterReflection;

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

    public function getTypeHint(): string
    {
        // @todo: try only (string) $this->betterParameterReflection->getTypeHint()
        if ($this->betterParameterReflection->isArray()) {
            return 'array';
        }

        if ($this->betterParameterReflection->isCallable()) {
            return 'callable';
        }

        $typeHint = $this->betterParameterReflection->getTypeHint();
        if ($typeHint instanceof Object_) {
            $classOrInterfaceName = (string) $typeHint->getFqsen();
            return ltrim($classOrInterfaceName, '\\');
        }

        if (count($this->betterParameterReflection->getDocBlockTypes())) {
            return implode('|', $this->betterParameterReflection->getDocBlockTypeStrings());
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
        if ($this->betterParameterReflection->isDefaultValueAvailable()) {
            if ($this->betterParameterReflection->isDefaultValueConstant()) {
                return $this->betterParameterReflection->getDefaultValueConstantName();
            }

            return $this->betterParameterReflection->getDefaultValueAsString();
        }

        return null;
    }

    public function isArray(): bool
    {
        return $this->betterParameterReflection->isArray();
    }

    /**
     * @return ClassReflectionInterface|InterfaceReflectionInterface|null
     */
    public function getTypeHintClassOrInterfaceReflection()
    {
        if (! class_exists($this->getTypeHint())) {
            return null;
        }

        $betterClassReflection = ReflectionClass::createFromName($this->getTypeHint());

        /** @var ClassReflectionInterface|InterfaceReflectionInterface $classOrInterfaceReflection */
        $classOrInterfaceReflection = $this->transformerCollector->transformSingle($betterClassReflection);

        return $classOrInterfaceReflection;
    }

    public function isVariadic(): bool
    {
        return $this->betterParameterReflection->isVariadic();
    }

    public function isCallable(): bool
    {
        return $this->betterParameterReflection->isCallable();
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
