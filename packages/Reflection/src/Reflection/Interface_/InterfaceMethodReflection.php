<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Interface_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\AbstractParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\Contract\TransformerCollectorInterface;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class InterfaceMethodReflection implements InterfaceMethodReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionMethod
     */
    private $betterMethodReflection;

    /**
     * @var TransformerCollectorInterface
     */
    private $transformerCollector;

    /**
     * @var DocBlock
     */
    private $docBlock;

    public function __construct(ReflectionMethod $betterMethodReflection, DocBlock $docBlock)
    {
        $this->betterMethodReflection = $betterMethodReflection;
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

    /**
     * @return AbstractParameterReflectionInterface[]
     */
    public function getParameters(): array
    {
        return $this->transformerCollector->transformGroup(
            $this->betterMethodReflection->getParameters()
        );
    }

    public function getDeclaringInterface(): InterfaceReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterMethodReflection->getDeclaringClass()
        );
    }

    public function getDeclaringInterfaceName(): string
    {
        return $this->getDeclaringInterface()
            ->getName();
    }

    public function setTransformerCollector(TransformerCollectorInterface $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }

    public function isDeprecated(): bool
    {
        return $this->hasAnnotation(AnnotationList::DEPRECATED);
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
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
}
