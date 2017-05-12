<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Annotation\AnnotationList;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ReflectionClass;

final class ClassConstantReflection implements ClassConstantReflectionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    /**
     * @var mixed
     */
    private $nativeClassConstantReflection;

    /**
     * @param mixed $value
     */
    private function __construct(string $name, $value, ClassReflectionInterface $classReflection)
    {
        $this->name = $name;
        $this->value = $value;
        $this->classReflection = $classReflection;

        $nativeClassConstantReflection = new ReflectionClass($classReflection->getName());
        $this->nativeClassConstantReflection = $nativeClassConstantReflection->getConstant($name);
    }

    /**
     * @param mixed $value
     */
    public static function createFromNameValueAndClass(string $name, $value, ClassReflectionInterface $classReflection): self
    {
        return new self($name, $value, $classReflection);
    }

    public function isPublic(): bool
    {
        return $this->nativeClassConstantReflection->isPublic();
    }

    public function isProtected(): bool
    {
        return $this->nativeClassConstantReflection->isProtected();
    }

    public function isPrivate(): bool
    {
        return $this->nativeClassConstantReflection->isPrivate();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTypeHint(): string
    {
        $annotations = $this->getAnnotation(AnnotationList::VAR_);
        dump($annotations);
        die;

        if ($annotations) {
            [$types] = preg_split('~\s+|$~', $annotations[0], 2);
            if (! empty($types)) {
                return $types;
            }
        }

        try {
            $type = gettype($this->getValue());
            if (strtolower($type) !== 'null') {
                return $type;
            }
        } catch (Exception $exception) {
            return '';
        }
    }

    public function getDeclaringClass(): ClassReflectionInterface
    {
        return $this->classReflection;
    }

    public function getDeclaringClassName(): string
    {
        return $this->classReflection->getName();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getValueDefinition(): string
    {
        // what for?
        return $this->reflection->getValueDefinition();
    }

    public function isDeprecated(): bool
    {
        if ($this->classReflection->isDeprecated()) {
            return true;
        }

        // if parent is deprecated, so is this
    }

    /**
     *
     * @return mixed[]
     */
    public function getAnnotations(): array
    {
        // TODO: Implement getAnnotations() method.
    }

    /**
     * @return mixed[]
     */
    public function getAnnotation(string $name): array
    {
        return $this->docBlock->getTagsByName($name);
    }

    public function hasAnnotation(string $name): bool
    {
        // TODO: Implement hasAnnotation() method.
    }

    public function getDescription(): string
    {
        $description = $this->docBlock->getSummary()
            . AnnotationList::EMPTY_LINE
            . $this->docBlock->getDescription();

        return trim($description);
    }

    public function getStartLine(): int
    {
        // TODO: Implement getStartLine() method.
    }

    public function getEndLine(): int
    {
        // TODO: Implement getEndLine() method.
    }
}
