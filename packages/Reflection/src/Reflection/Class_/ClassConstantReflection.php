<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use phpDocumentor\Reflection\DocBlock\Tag;
use PhpParser\Node\Stmt\Class_;

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
    private $modifier;

    /**
     * @param mixed $value
     */
    private function __construct(string $name, $value, ClassReflectionInterface $classReflection)
    {
        $this->name = $name;
        $this->value = $value;
        $this->classReflection = $classReflection;
    }

    /**
     * @param mixed $value
     */
    public static function createFromNameValueAndClass(string $name, $value, ClassReflectionInterface $classReflection): self
    {
        return new self($name, $value, $classReflection);
    }

    /**
     * @todo use PHP-Parser to get modifier
     */
    public function isPublic(): bool
    {
        return true;
        // return (bool) ($this->modifier & Class_::MODIFIER_PUBLIC);
    }

    /**
     * @todo use PHP-Parser to get modifier
     */
    public function isProtected(): bool
    {
        return false;
    }

    /**
     * @todo use PHP-Parser to get modifier
     */
    public function isPrivate(): bool
    {
        return false;
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
        // @todo
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
