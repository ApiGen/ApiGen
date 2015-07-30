<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

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


    /**
     * @return string
     */
    public function elementUrl(ElementReflectionInterface $element)
    {
        return $this->elementUrlFactory->createForElement($element);
    }


    /**
     * @param string|ReflectionClass $class
     * @return string
     */
    public function classUrl($class)
    {
        return $this->elementUrlFactory->createForClass($class);
    }


    /**
     * @return string
     */
    public function methodUrl(MethodReflectionInterface $method, ClassReflectionInterface $class = null)
    {
        return $this->elementUrlFactory->createForMethod($method, $class);
    }


    /**
     * @return string
     */
    public function propertyUrl(PropertyReflectionInterface $property, ClassReflectionInterface $class = null)
    {
        return $this->elementUrlFactory->createForProperty($property, $class);
    }


    /**
     * @return string
     */
    public function constantUrl(ConstantReflectionInterface $constant)
    {
        return $this->elementUrlFactory->createForConstant($constant);
    }


    /**
     * @return string
     */
    public function functionUrl(FunctionReflectionInterface $function)
    {
        return $this->elementUrlFactory->createForFunction($function);
    }
}
