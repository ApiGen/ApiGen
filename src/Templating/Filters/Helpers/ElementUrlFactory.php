<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters\Helpers;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
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

        if ($element instanceof MethodReflectionInterface) {
            return $this->createForMethod($element);
        }

        if ($element instanceof PropertyReflectionInterface) {
            return $this->createForProperty($element);
        }

        if ($element instanceof ConstantReflectionInterface) {
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

    public function createForMethod(MethodReflectionInterface $method, ?ClassReflectionInterface $class = null): string
    {
        $className = $class !== null ? $class->getName() : $method->getDeclaringClassName();
        return $this->createForClass($className) . '#_'
            . ($method->getOriginalName() ?: $method->getName());
    }

    public function createForProperty(
        PropertyReflectionInterface $property,
        ?ClassReflectionInterface $class = null
    ): string {
        $className = $class !== null ? $class->getName() : $property->getDeclaringClassName();
        return $this->createForClass($className) . '#$' . $property->getName();
    }

    public function createForConstant(ConstantReflectionInterface $constant): string
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
