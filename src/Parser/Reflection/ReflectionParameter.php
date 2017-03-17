<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\AbstractFunctionMethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;

class ReflectionParameter extends ReflectionBase implements ParameterReflectionInterface
{

    public function getTypeHint(): string
    {
        if ($this->isArray()) {
            return 'array';
        } elseif ($this->isCallable()) {
            return 'callable';
        } elseif ($className = $this->getClassName()) {
            return $className;
        } elseif ($annotations = $this->getDeclaringFunction()->getAnnotation('param')) {
            if (! empty($annotations[$this->getPosition()])) {
                [$types] = preg_split('~\s+|$~', $annotations[$this->getPosition()], 2);
                if (! empty($types) && $types[0] !== '$') {
                    return $types;
                }
            }
        }
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


    public function isDefaultValueAvailable(): bool
    {
        return $this->reflection->isDefaultValueAvailable();
    }


    public function getPosition(): int
    {
        return $this->reflection->getPosition();
    }


    public function isArray(): bool
    {
        return $this->reflection->isArray();
    }


    public function isCallable(): bool
    {
        return $this->reflection->isCallable();
    }


    public function getClass(): ?ClassReflectionInterface
    {
        $className = $this->reflection->getClassName();
        return $className === null ? null : $this->getParsedClasses()[$className];
    }


    public function getClassName(): ?string
    {
        return $this->reflection->getClassName();
    }


    public function allowsNull(): bool
    {
        return $this->reflection->allowsNull();
    }


    public function isOptional(): bool
    {
        return $this->reflection->isOptional();
    }


    public function isPassedByReference(): bool
    {
        return $this->reflection->isPassedByReference();
    }


    public function canBePassedByValue(): bool
    {
        return $this->reflection->canBePassedByValue();
    }


    public function getDeclaringFunction(): AbstractFunctionMethodReflectionInterface
    {
        $functionName = $this->reflection->getDeclaringFunctionName();

        if ($className = $this->reflection->getDeclaringClassName()) {
            return $this->getParsedClasses()[$className]->getMethod($functionName);
        }

        return $this->parserResult->getFunctions()[$functionName];
    }


    public function getDeclaringFunctionName(): string
    {
        return $this->reflection->getDeclaringFunctionName();
    }


    public function getDeclaringClass(): ?ClassReflectionInterface
    {
        $className = $this->reflection->getDeclaringClassName();
        return $className === null ? null : $this->getParsedClasses()[$className];
    }


    public function getDeclaringClassName(): string
    {
        return $this->reflection->getDeclaringClassName();
    }


    public function isUnlimited(): bool
    {
        return false;
    }
}
