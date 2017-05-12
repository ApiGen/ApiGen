<?php declare(strict_types=1);

namespace ApiGen\Reflection\Reflection\Class_;

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

    public function getStartLine(): int
    {
        // TODO: Implement getStartLine() method.
    }

    public function getEndLine(): int
    {
        // TODO: Implement getEndLine() method.
    }
}
