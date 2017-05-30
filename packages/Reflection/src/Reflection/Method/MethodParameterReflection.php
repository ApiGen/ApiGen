<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Method;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Types\Object_;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionParameter;

final class MethodParameterReflection implements MethodParameterReflectionInterface, TransformerCollectorAwareInterface
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

    public function getTypeHint(): string
    {
        if ($this->isArray()) {
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

        $annotation = $this->getAnnotation();
        if ($annotation) {
            return (string) $annotation->getType();
        }

        return '';
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

    public function isVariadic(): bool
    {
        return $this->betterParameterReflection->isVariadic();
    }

    public function isCallable(): bool
    {
        return $this->betterParameterReflection->isCallable();
    }

    private function getAnnotation(): ?Param
    {
        $annotations = $this->getDeclaringFunction()
            ->getAnnotation(AnnotationList::PARAM);

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

    public function getDeclaringMethodName(): string
    {
        return $this->getDeclaringMethod()
            ->getName();
    }

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
