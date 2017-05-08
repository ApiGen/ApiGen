<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Function_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionFunction;

final class FunctionReflection implements FunctionReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionFunction
     */
    private $reflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    public function __construct(ReflectionFunction $betterFunctionReflection, DocBlock $docBlock)
    {
        $this->reflection = $betterFunctionReflection;
        $this->docBlock = $docBlock;
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
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    /**
     * @return FunctionParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->reflection->getParameters()
        );
    }

    public function isDocumented(): bool
    {
        if ($this->reflection->isInternal()) {
            return false;
        }

        if ($this->hasAnnotation('internal')) {
            return false;
        }

        return true;
    }

    public function getFileName(): string
    {
        return $this->reflection->getFileName();
    }

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
