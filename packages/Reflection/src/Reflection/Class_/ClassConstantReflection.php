<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use phpDocumentor\Reflection\DocBlock\Tag;
use ReflectionClass;
use ReflectionClassConstant;

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
     * Note: php bug, not documented: https://bugs.php.net/bug.php?id=74261
     *
     * @var ReflectionClassConstant
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

        $nativeClassReflection = new ReflectionClass($classReflection->getName());
        $this->nativeClassConstantReflection = $nativeClassReflection->getReflectionConstant($name);
    }

    /**
     * @param mixed $value
     */
    public static function createFromNameValueAndClass(
        string $name,
        $value,
        ClassReflectionInterface $classReflection
    ): self {
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
        $valueType = gettype($this->value);
        if ($valueType === 'integer') {
            return 'int';
        }

        return $valueType;
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

    public function isDeprecated(): bool
    {
        if ($this->classReflection->isDeprecated()) {
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
}
