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

    public function getTypeHints(): array
    {
        $typeHint = $this->betterParameterReflection->getTypeHint();

        if ($typeHint) {
            $typeHint = ltrim((string) $typeHint, '\\');
            return [$typeHint];
        }

        $annotation = $this->getAnnotation();
        if ($annotation) {
            $types = explode('|', (string) $annotation->getType());
            return $this->resolveTypes($types);
        }

        return [];
    }

    /**
     * Resolves fully-qualified class names in type hints.
     *
     * In BetterReflection library, there is no support (and shall not be)
     * for unnamed `@param` annotations being defined by their index. For
     * that reason, we need to process the annotations on our own. However,
     * using the indexed `@param` annotations is not allowed by PSR-5, so
     * it is possible to deprecate this function in the future.
     *
     * @param string[] $types
     * @return string[]
     */
    private function resolveTypes(array $types): array
    {
        array_walk($types, function (&$value) {
           $value = ltrim($value, '\\');
        });

        $function = $this->betterParameterReflection->getDeclaringFunction();
        if ($function instanceof \Roave\BetterReflection\Reflection\ReflectionMethod) {
            $function = $function->getDeclaringClass();
        }

        $context = (new \phpDocumentor\Reflection\Types\ContextFactory())->createForNamespace(
            $function->getNamespaceName(),
            $function->getLocatedSource()->getSource()
        );

        $types = (new \Roave\BetterReflection\TypesFinder\ResolveTypes())->__invoke($types, $context);
        array_walk($types, function (&$value) {
           $value = ltrim((string) $value, '\\');
        });

        return $types;
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
