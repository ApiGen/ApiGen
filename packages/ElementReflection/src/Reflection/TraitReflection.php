<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Reflection;

use ApiGen\Annotation\AnnotationList;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class TraitReflection
{
    /**
     * @var ReflectionClass
     */
    private $reflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    public function __construct(
        ReflectionClass $betterClassReflection,
        DocBlock $docBlock
    ) {
        $this->reflection = $betterClassReflection;
        $this->docBlock = $docBlock;
    }

    public function getName(): string
    {
        return $this->reflection->getName();
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
}
