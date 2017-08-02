<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class ClassMethodReflection implements ClassMethodReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var string
     */
    private const EMPTY_LINE = PHP_EOL . PHP_EOL;

    /**
     * @var ReflectionMethod
     */
    private $betterMethodReflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollector
     */
    private $transformerCollector;

    public function __construct(ReflectionMethod $betterFunctionReflection, DocBlock $docBlock)
    {
        $this->betterMethodReflection = $betterFunctionReflection;
        $this->docBlock = $docBlock;
    }

    public function getName(): string
    {
        return $this->betterMethodReflection->getName();
    }

    public function getShortName(): string
    {
        return $this->betterMethodReflection->getShortName();
    }

    public function getStartLine(): int
    {
        return $this->betterMethodReflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->betterMethodReflection->getEndLine();
    }

    public function returnsReference(): bool
    {
        return $this->betterMethodReflection->returnsReference();
    }

    public function isDeprecated(): bool
    {
        if ($this->betterMethodReflection->isDeprecated()) {
            return true;
        }

        return $this->getDeclaringClass()
            ->isDeprecated();
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
     * @return Tag[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    public function getDescription(): string
    {
        if ($this->docBlock->hasTag('inheritdoc')) {
            if ($this->getImplementedMethod()) {
                return $this->getImplementedMethod()
                    ->getDescription();
            }

            if ($this->getOverriddenMethod()) {
                return $this->getOverriddenMethod()
                    ->getDescription();
            }
        }

        $description = $this->docBlock->getSummary()
            . self::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function getDeclaringClass(): ClassReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterMethodReflection->getDeclaringClass()
        );
    }

    public function getDeclaringClassName(): string
    {
        return $this->getDeclaringClass()
            ->getName();
    }

    public function isPrivate(): bool
    {
        return $this->betterMethodReflection->isPrivate();
    }

    public function isProtected(): bool
    {
        return $this->betterMethodReflection->isProtected();
    }

    public function isPublic(): bool
    {
        return $this->betterMethodReflection->isPublic();
    }

    public function isAbstract(): bool
    {
        return $this->betterMethodReflection->isAbstract();
    }

    public function isFinal(): bool
    {
        return $this->betterMethodReflection->isFinal();
    }

    public function isStatic(): bool
    {
        return $this->betterMethodReflection->isStatic();
    }

    public function getImplementedMethod(): ?InterfaceMethodReflectionInterface
    {
        foreach ($this->getDeclaringClass()->getOwnInterfaces() as $interface) {
            if (isset($interface->getMethods()[$this->getName()])) {
                return $interface->getMethod($this->getName());
            }
        }

        return null;
    }

    // @todo is used?
    public function getOverriddenMethod(): ?ClassMethodReflectionInterface
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
     * @return MethodParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterMethodReflection->getParameters()
        );
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
