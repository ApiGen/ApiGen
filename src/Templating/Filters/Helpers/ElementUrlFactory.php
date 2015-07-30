<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters\Helpers;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Theme\ThemeConfigOptions as TCO;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Templating\Filters\Filters;

class ElementUrlFactory
{

    /**
     * @var ConfigurationInterface
     */
    private $configuration;


    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @param ElementReflectionInterface|string $element
     * @return string|NULL
     */
    public function createForElement($element)
    {
        if ($element instanceof ClassReflectionInterface) {
            return $this->createForClass($element);

        } elseif ($element instanceof MethodReflectionInterface) {
            return $this->createForMethod($element);

        } elseif ($element instanceof PropertyReflectionInterface) {
            return $this->createForProperty($element);

        } elseif ($element instanceof ConstantReflectionInterface) {
            return $this->createForConstant($element);

        } elseif ($element instanceof FunctionReflectionInterface) {
            return $this->createForFunction($element);
        }

        return null;
    }


    /**
     * @param string|ClassReflectionInterface $class
     * @return string
     */
    public function createForClass($class)
    {
        $className = $class instanceof ClassReflectionInterface ? $class->getName() : $class;
        return sprintf(
            $this->configuration->getOption(CO::TEMPLATE)['templates']['class']['filename'],
            Filters::urlize($className)
        );
    }


    /**
     * @return string
     */
    public function createForMethod(MethodReflectionInterface $method, ClassReflectionInterface $class = null)
    {
        $className = $class !== null ? $class->getName() : $method->getDeclaringClassName();
        return $this->createForClass($className) . '#' . ($method->isMagic() ? 'm' : '') . '_'
        . ($method->getOriginalName() ?: $method->getName());
    }


    /**
     * @return string
     */
    public function createForProperty(PropertyReflectionInterface $property, ClassReflectionInterface $class = null)
    {
        $className = $class !== null ? $class->getName() : $property->getDeclaringClassName();
        return $this->createForClass($className) . '#' . ($property->isMagic() ? 'm' : '') . '$' . $property->getName();
    }


    /**
     * @return string
     */
    public function createForConstant(ConstantReflectionInterface $constant)
    {
        // Class constant
        if ($className = $constant->getDeclaringClassName()) {
            return $this->createForClass($className) . '#' . $constant->getName();
        }

        // Constant in namespace or global space
        return sprintf(
            $this->configuration->getOption(CO::TEMPLATE)['templates']['constant']['filename'],
            Filters::urlize($constant->getName())
        );
    }


    /**
     * @return string
     */
    public function createForFunction(FunctionReflectionInterface $function)
    {
        return sprintf(
            $this->configuration->getOption(CO::TEMPLATE)['templates']['function']['filename'],
            Filters::urlize($function->getName())
        );
    }


    /**
     * @param string $name
     * @return string
     */
    public function createForAnnotationGroup($name)
    {
        return sprintf(
            $this->configuration->getOption(CO::TEMPLATE)['templates'][TCO::ANNOTATION_GROUP]['filename'],
            Filters::urlize($name)
        );
    }
}
