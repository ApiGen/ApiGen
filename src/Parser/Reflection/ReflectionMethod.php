<?php declare(strict_types=1);

namespace ApiGen\Parser\Reflection;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Parser\Reflection\Parts\Visibility;

class ReflectionMethod extends ReflectionFunctionBase implements MethodReflectionInterface
{

    use Visibility;


    public function isMagic(): bool
    {
        return false;
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


    public function isAbstract(): bool
    {
        return $this->reflection->isAbstract();
    }


    public function isFinal(): bool
    {
        return $this->reflection->isFinal();
    }


    public function isStatic(): bool
    {
        return $this->reflection->isStatic();
    }


    public function getDeclaringTrait(): ?ClassReflectionInterface
    {
        $traitName = $this->reflection->getDeclaringTraitName();
        return $traitName === null ? null : $this->getParsedClasses()[$traitName];
    }


    public function getDeclaringTraitName(): string
    {
        return $this->reflection->getDeclaringTraitName();
    }


    public function getOriginalName(): string
    {
        return $this->reflection->getOriginalName();
    }


    public function isValid(): bool
    {
        if ($class = $this->getDeclaringClass()) {
            return $class->isValid();
        }

        return true;
    }


    public function getImplementedMethod(): ?MethodReflectionInterface
    {
        foreach ($this->getDeclaringClass()->getOwnInterfaces() as $interface) {
            if ($interface->hasMethod($this->getName())) {
                return $interface->getMethod($this->getName());
            }
        }
        return null;
    }


    public function getOverriddenMethod(): ?MethodReflectionInterface
    {
        $parent = $this->getDeclaringClass()->getParentClass();
        if ($parent === null) {
            return null;
        }
        foreach ($parent->getMethods() as $method) {
            if ($method->getName() === $this->getName()) {
                if (! $method->isPrivate() && ! $method->isAbstract()) {
                    return $method;
                } else {
                    return null;
                }
            }
        }
        return null;
    }


    public function getOriginal(): ?MethodReflectionInterface
    {
        $originalName = $this->reflection->getOriginalName();
        if ($originalName === null) {
            return null;
        }
        $originalDeclaringClassName = $this->reflection->getOriginal()->getDeclaringClassName();
        return $this->getParsedClasses()[$originalDeclaringClassName]->getMethod($originalName);
    }
}
