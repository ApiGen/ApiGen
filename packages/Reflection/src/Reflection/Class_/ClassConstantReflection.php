<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\TransformerCollectorAwareInterface;
use ApiGen\Reflection\TransformerCollector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

final class ClassConstantReflection implements ClassConstantReflectionInterface, TransformerCollectorAwareInterface
{
    /**
     * @var ReflectionClassConstant
     */
    private $constant;

    /**
     * @var DocBlock
     */
    private $docBlock;

    /**
     * @var TransformerCollector
     */
    private $transformerCollector;


    /**
     * @param string $name
     * @param mixed $value
     * @param string $visibility
     * @param ClassReflectionInterface $classReflection
     */
    public function __construct(ReflectionClassConstant $constant, DocBlock $docBlock)
    {
        $this->constant = $constant;
        $this->docBlock = $docBlock;
    }

    public function isPublic(): bool
    {
        return $this->constant->isPublic();
    }

    public function isProtected(): bool
    {
        return $this->constant->isProtected();
    }

    public function isPrivate(): bool
    {
        return $this->constant->isPrivate();
    }

    public function getName(): string
    {
        return $this->constant->getName();
    }

    public function getTypeHint(): string
    {
        $valueType = gettype($this->constant->getValue());
        if ($valueType === 'integer') {
            return 'int';
        }

        return $valueType;
    }

    public function getDeclaringClass(): ClassReflectionInterface
    {
        return $this->transformerCollector->transformSingle(
            $this->constant->getDeclaringClass()
        );
    }

    public function getDeclaringClassName(): string
    {
        return $this->constant->getDeclaringClass()->getName();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->constant->getValue();
    }

    public function isDeprecated(): bool
    {
        if ($this->getDeclaringClass()->isDeprecated()) {
            return true;
        }

        return false;
    }

    /**
     * @todo Complete, probably from tokenizer.
     * Inspiration https://github.com/POPSuL/PHP-Token-Reflection/blob/8883ecd6f63a2ac8e97a3f7ef4529484a8e57ddf/TokenReflection/ReflectionElement.php#L291-L305
     */
    public function getStartLine(): int
    {
        return 25;
    }

    /**
     * @todo Complete
     */
    public function getEndLine(): int
    {
        return 35;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function hasAnnotation(string $name): bool
    {
        // @todo
        return false;
    }

    /**
     * @return Tag[]
     */
    public function getAnnotation(string $name): array
    {
        // @todo
        return [];
    }

    /**
     * @return Tag[]|Tag[][]
     */
    public function getAnnotations(): array
    {
        // @todo
        return [];
    }

    public function setTransformerCollector(TransformerCollector $transformerCollector): void
    {
        $this->transformerCollector = $transformerCollector;
    }
}
