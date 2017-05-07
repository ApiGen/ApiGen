<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\MethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\PropertyReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;

final class ElementUrlFilters extends Filters
{
    /**
     * @var ElementUrlFactory
     */
    private $elementUrlFactory;

    public function __construct(ElementUrlFactory $elementUrlFactory)
    {
        $this->elementUrlFactory = $elementUrlFactory;
    }

    public function elementUrl(ReflectionInterface $element): string
    {
        return $this->elementUrlFactory->createForElement($element);
    }

    /**
     * @param string|ClassReflectionInterface $class
     */
    public function classUrl($class): string
    {
        return $this->elementUrlFactory->createForClass($class);
    }

    public function methodUrl(MethodReflectionInterface $method, ?ClassReflectionInterface $class = null): string
    {
        return $this->elementUrlFactory->createForMethod($method, $class);
    }

    public function propertyUrl(PropertyReflectionInterface $property, ?ClassReflectionInterface $class = null): string
    {
        return $this->elementUrlFactory->createForProperty($property, $class);
    }

    public function constantUrl(ClassConstantReflectionInterface $constant): string
    {
        return $this->elementUrlFactory->createForConstant($constant);
    }

    public function functionUrl(FunctionReflectionInterface $function): string
    {
        return $this->elementUrlFactory->createForFunction($function);
    }
}
