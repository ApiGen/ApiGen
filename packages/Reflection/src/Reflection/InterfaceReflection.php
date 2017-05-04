<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

/**
 * @todo prepare interface
 */
final class InterfaceReflection
{
    /**
     * @var ReflectionClass
     */
    private $betterInterfaceReflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    public function __construct(ReflectionClass $betterInterfaceReflection, DocBlock $docBlock)
    {
        $this->betterInterfaceReflection = $betterInterfaceReflection;
        $this->docBlock = $docBlock;
    }

    public function getStartLine(): int
    {
        return $this->betterInterfaceReflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->betterInterfaceReflection->getEndLine();
    }

    public function getName(): string
    {
        return $this->betterInterfaceReflection->getName();
    }

    public function getShortName(): string
    {
        return $this->betterInterfaceReflection->getShortName();
    }

    public function getPrettyName(): string
    {
        return $this->betterInterfaceReflection->getName() . '()';
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
     * @return ClassReflectionInterface[]
     */
    public function getDirectImplementers(): array
    {
        if (! $this->isInterface()) {
            return [];
        }

        return $this->parserStorage->getDirectImplementersOfInterface($this);
    }

    /**
     * @return ClassReflectionInterface[]
     */
    public function getIndirectImplementers(): array
    {
        if (! $this->isInterface()) {
            return [];
        }

        return $this->parserStorage->getIndirectImplementersOfInterface($this);
    }
}
