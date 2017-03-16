<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;

class ElementUrlFilters extends Filters
{

    /**
     * @var ElementUrlFactory
     */
    private $elementUrlFactory;


    public function __construct(ElementUrlFactory $elementUrlFactory)
    {
        $this->elementUrlFactory = $elementUrlFactory;
    }


    public function elementUrl(ElementReflectionInterface $element): string
    {
        return $this->elementUrlFactory->createForElement($element);
    }


    /**
     * @param string|ReflectionClass $class
     * @return string
     */
    public function classUrl($class): string
    {
        return $this->elementUrlFactory->createForClass($class);
    }


    public function methodUrl(MethodReflectionInterface $method, ClassReflectionInterface $class = null): string
    {
        return $this->elementUrlFactory->createForMethod($method, $class);
    }


    public function propertyUrl(PropertyReflectionInterface $property, ClassReflectionInterface $class = null): string
    {
        return $this->elementUrlFactory->createForProperty($property, $class);
    }


    public function constantUrl(ConstantReflectionInterface $constant): string
    {
        return $this->elementUrlFactory->createForConstant($constant);
    }


    public function functionUrl(FunctionReflectionInterface $function): string
    {
        return $this->elementUrlFactory->createForFunction($function);
    }
}
