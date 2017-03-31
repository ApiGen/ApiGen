<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\AbstractFunctionMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;

final class ReflectionParameter extends AbstractReflection implements ParameterReflectionInterface
{
    public function getTypeHint(): string
    {
        if ($this->isArray()) {
            return 'array';
        }

        if ($this->isCallable()) {
            return 'callable';
        }

        $className = $this->getClassName();
        if ($className) {
            return $className;
        }

        $annotations = $this->getDeclaringFunction()->getAnnotation('param');
        if ($annotations) {
            if (! empty($annotations[$this->getPosition()])) {
                [$types] = preg_split('~\s+|$~', $annotations[$this->getPosition()], 2);
                if (! empty($types) && $types[0] !== '$') {
                    return $types;
                }
            }
        }

        return '';
    }

    public function getDescription(): string
    {
        $annotations = $this->getDeclaringFunction()->getAnnotation('param');
        if (empty($annotations[$this->getPosition()])) {
            return '';
        }

        $description = trim(strpbrk($annotations[$this->getPosition()], "\n\r\t "));
        return preg_replace('~^(\\$' . $this->getName() . '(?:,\\.{3})?)(\\s+|$)~i', '\\2', $description, 1);
    }

    public function getDefaultValueDefinition(): ?string
    {
        return $this->reflection === null ? null : $this->reflection->getDefaultValueDefinition();
    }

    public function getPosition(): int
    {
        return $this->reflection->getPosition();
    }

    public function isArray(): bool
    {
        return $this->reflection->isArray();
    }

    public function getClass(): ?ClassReflectionInterface
    {
        $className = (string) $this->reflection->getClassName();
        return $className === '' ? null : $this->getParsedClasses()[$className];
    }

    public function getClassName(): ?string
    {
        return $this->reflection->getClassName();
    }

    public function isOptional(): bool
    {
        return $this->reflection->isOptional();
    }

    public function canBePassedByValue(): bool
    {
        return $this->reflection->canBePassedByValue();
    }

    public function getDeclaringFunction(): AbstractFunctionMethodReflectionInterface
    {
        $functionName = $this->reflection->getDeclaringFunctionName();

        $className = $this->reflection->getDeclaringClassName();

        if ($className) {
            return $this->getParsedClasses()[$className]->getMethod($functionName);
        }

        return $this->parserStorage->getFunctions()[$functionName];
    }

    public function getDeclaringFunctionName(): string
    {
        return (string) $this->reflection->getDeclaringFunctionName();
    }

    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        $className = $this->reflection->getDeclaringClassName();
        return $className === '' ? null : $this->getParsedClasses()[$className];
    }

    public function getDeclaringClassName(): string
    {
        return (string) $this->reflection->getDeclaringClassName();
    }

    public function isUnlimited(): bool
    {
        return false;
    }

    private function isCallable(): bool
    {
        return $this->reflection->isCallable();
    }
}
