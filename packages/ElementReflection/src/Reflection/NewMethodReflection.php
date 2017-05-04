<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TraitReflectionInterface;
use ApiGen\ReflectionToElementTransformer\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class NewMethodReflection implements MethodReflectionInterface
{
    /**
     * @var string
     */
    private const EMPTY_LINE = PHP_EOL . PHP_EOL;

    /**
     * @var ReflectionMethod
     */
    private $reflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var ClassReflectionInterface
     */
    private $declaringClass;

    /**
     * @var ParameterReflectionInterface[]
     */
    private $parameters = [];

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    /**
     * @param ParameterReflectionInterface[] $parameterReflections
     */
    public function __construct(
        ReflectionMethod $betterFunctionReflection,
        DocBlock $docBlock,
        TransformerCollectorInterface $transformerCollector
    ) {
        $this->reflection = $betterFunctionReflection;
        $this->docBlock = $docBlock;
        $this->transformerCollector = $transformerCollector;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function getStartLine(): int
    {
        return $this->reflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->reflection->getEndLine();
    }

    public function returnsReference(): bool
    {
        return $this->reflection->returnsReference();
    }

    public function isDeprecated(): bool
    {
        return $this->reflection->isDeprecated();
    }

    public function getNamespaceName(): string
    {
        return $this->reflection->getNamespaceName();
    }

    public function getPseudoNamespaceName(): string
    {
        if ($this->reflection->isInternal()) {
            return 'PHP';
        }

        if ($this->reflection->getNamespaceName()) {
            return $this->reflection->getNamespaceName();
        }

        return 'None';
    }

    public function getPrettyName(): string
    {
        return $this->reflection->getName() . '()';
    }

    /**
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        return $this->docBlock->getTags();
    }

    public function hasAnnotation(string $name): bool
    {
        return $this->docBlock->hasTag($name);
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . self::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function isDocumented(): bool
    {
        if ($this->reflection->isInternal()) { // @note: what exactly does this mean? PHP or OUR?
            return false;
        }

        if ($this->hasAnnotation('internal')) {
            return false;
        }

        return true;
    }

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        return $this->declaringClass;
    }

    public function getDeclaringClassName(): string
    {
        if ($this->declaringClass) {
            $this->declaringClass->getName();
        }

        return '';
    }

    public function getDeclaringTrait(): ?TraitReflectionInterface
    {
        if ($this->reflection->getDeclaringClass()->isTrait()) {
            return $this->transformerCollector->transformReflectionToElement($this->reflection->getDeclaringClass());
        }

        return null;
    }

    public function getDeclaringTraitName(): string
    {
        if (! $this->getDeclaringTrait()) {
            return '';
        }

        return $this->getDeclaringTrait()
            ->getName();
    }

    public function setDeclaringClass(ClassReflectionInterface $classReflection): void
    {
        $this->declaringClass = $classReflection;
    }

    public function isPrivate(): bool
    {
        return $this->reflection->isPrivate();
    }

    public function isProtected(): bool
    {
        return $this->reflection->isProtected();
    }

    public function isPublic(): bool
    {
        return $this->reflection->isPublic();
    }

    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->reflection->isFinal();
    }

    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }

    // @todo: is used?
    public function getImplementedMethod(): ?MethodReflectionInterface
    {
        foreach ($this->getDeclaringClass()->getOwnInterfaces() as $interface) {
            if ($interface->hasMethod($this->getName())) {
                return $interface->getMethod($this->getName());
            }
        }

        return null;
    }

    // @todo: is used?
    public function getOverriddenMethod(): ?MethodReflectionInterface
    {
        $parent = $this->getDeclaringClass()->getParentClass();
        if ($parent === null) {
            return null;
        }

        foreach ($parent->getMethods() as $method) {
            if ($method->getName() === $this->getName()) {
                if (! $method->isPrivate() && ! $method->isAbstract()) {
                    return $method;
                }

                return null;
            }
        }

        return null;
    }

    /**
     * @return ParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        if ($this->parameters === []) {
            $this->parameters = $this->transformerCollector->transformReflectionsToElements(
                $this->reflection->getParameters()
            );
        }

        return $this->parameters;
    }
}
