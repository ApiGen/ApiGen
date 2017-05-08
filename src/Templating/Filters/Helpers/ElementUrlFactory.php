<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters\Helpers;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassPropertyReflectionInterface;
use ApiGen\Templating\Filters\Filters;

final class ElementUrlFactory
{
    /**
     * @param ReflectionInterface|string $element
     */
    public function createForElement($element): ?string
    {
        if ($element instanceof ClassReflectionInterface) {
            return $this->createForClass($element);
        }

        if ($element instanceof ClassMethodReflectionInterface) {
            return $this->createForMethod($element);
        }

        if ($element instanceof ClassPropertyReflectionInterface) {
            return $this->createForProperty($element);
        }

        if ($element instanceof ClassConstantReflectionInterface) {
            return $this->createForConstant($element);
        }

        if ($element instanceof FunctionReflectionInterface) {
            return $this->createForFunction($element);
        }

        return null;
    }

    /**
     * @param string|ClassReflectionInterface $class
     */
    public function createForClass($class): string
    {
        $className = $class instanceof ClassReflectionInterface ? $class->getName() : $class;

        $filename = 'class-%s.html';
        if ($class instanceof ClassReflectionInterface) {
            if ($class->isTrait()) {
                $filename = 'trait-%s.html';
            } elseif ($class->isInterface()) {
                $filename = 'interface-%s.html';
            }
        }

        return sprintf($filename, Filters::urlize($className));
    }

    public function createForMethod(ClassMethodReflectionInterface $method, ?ClassReflectionInterface $class = null): string
    {
        $className = $class !== null ? $class->getName() : $method->getDeclaringClassName();
        return $this->createForClass($className) . '#_'
            . ($method->getOriginalName() ?: $method->getName());
    }

    public function createForProperty(
        ClassPropertyReflectionInterface $property,
        ?ClassReflectionInterface $class = null
    ): string {
        $className = $class !== null ? $class->getName() : $property->getDeclaringClassName();
        return $this->createForClass($className) . '#$' . $property->getName();
    }

    public function createForConstant(ClassConstantReflectionInterface $constant): string
    {
        $className = $constant->getDeclaringClassName();

        return $this->createForClass($className) . '#' . $constant->getName();
    }

    public function createForFunction(FunctionReflectionInterface $function): string
    {
        return sprintf(
            'function-%s.html',
            Filters::urlize($function->getName())
        );
    }
}
