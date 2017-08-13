<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Interface_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

/**
 * @inspiration https://github.com/POPSuL/PHP-Token-Reflection/blob/develop/TokenReflection/Php/ReflectionConstant.php
 */
final class InterfaceConstantReflection implements InterfaceConstantReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionClassConstant
     */
    private $betterConstantReflection;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollector
     */
    private $transformerCollector;

    public function __construct(ReflectionClassConstant $constant, DocBlock $docBlock)
    {
        $this->betterConstantReflection = $constant;
        $this->docBlock = $docBlock;
    }

    public function isPrivate(): bool
    {
        return $this->betterConstantReflection->isPrivate();
    }

    public function isProtected(): bool
    {
        return $this->betterConstantReflection->isProtected();
    }

    public function isPublic(): bool
    {
        return $this->betterConstantReflection->isPublic();
    }

    public function getName(): string
    {
        return $this->betterConstantReflection->getName();
    }

    public function getTypeHint(): string
    {
        $valueType = gettype($this->betterConstantReflection->getValue());
        if ($valueType === 'integer') {
            return 'int';
        }

        return $valueType;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->betterConstantReflection->getValue();
    }

    public function getValueDefinition(): string
    {
        return 'TODO'; // $this->constant->getValueAsString(); FIXME
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

    public function isDeprecated(): bool
    {
        if ($this->getDeclaringInterface()->isDeprecated()) {
            return true;
        }

        return $this->hasAnnotation(AnnotationList::DEPRECATED);
    }

    public function getStartLine(): int
    {
        return $this->betterConstantReflection->getStartLine();
    }

    public function getEndLine(): int
    {
        return $this->betterConstantReflection->getEndLine();
    }

    public function getDeclaringInterface(): InterfaceReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->betterConstantReflection->getDeclaringClass()
        );
    }

    public function getDeclaringInterfaceName(): string
    {
        return $this->getDeclaringInterface()
            ->getName();
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }

    public function getDescription(): string
    {
        return (string) $this->docBlock->getDescription();
    }
}
