<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Trait_;

use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class TraitMethodReflection implements TraitMethodReflectionInterface, TransformerCollectorAwareInterface
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

    public function getDeclaringTrait(): TraitReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterMethodReflection->getDeclaringClass()
        );
    }

    public function getDeclaringTraitName(): string
    {
        return $this->getDeclaringTrait()
            ->getName();
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

    public function returnsReference(): bool
    {
        return $this->betterMethodReflection->returnsReference();
    }

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterMethodReflection->getParameters()
        );
    }

    public function isDeprecated(): bool
    {
        if ($this->betterMethodReflection->isDeprecated()) {
            return true;
        }

        return $this->getDeclaringTrait()
            ->isDeprecated();
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . self::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function getOverriddenMethod(): ?TraitMethodReflectionInterface
    {
        return null;
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

    public function getStartLine(): int
    {
        return $this->betterMethodReflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->betterMethodReflection->getEndLine();
    }

    public function isPublic(): bool
    {
        return $this->betterMethodReflection->isPublic();
    }

    public function isProtected(): bool
    {
        return $this->betterMethodReflection->isProtected();
    }

    public function isPrivate(): bool
    {
        return $this->betterMethodReflection->isPrivate();
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
