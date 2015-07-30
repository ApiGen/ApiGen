<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating\Filters\Helpers;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use Nette\Utils\Html;
use UnexpectedValueException;

class ElementLinkFactory
{

    /**
     * @var ElementUrlFactory
     */
    private $elementUrlFactory;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;


    public function __construct(ElementUrlFactory $elementUrlFactory, LinkBuilder $linkBuilder)
    {
        $this->elementUrlFactory = $elementUrlFactory;
        $this->linkBuilder = $linkBuilder;
    }


    /**
     * @return string
     */
    public function createForElement(ElementReflectionInterface $element, array $classes = [])
    {
        if ($element instanceof ClassReflectionInterface) {
            return $this->createForClass($element, $classes);

        } elseif ($element instanceof MethodReflectionInterface) {
            return $this->createForMethod($element, $classes);

        } elseif ($element instanceof PropertyReflectionInterface) {
            return $this->createForProperty($element, $classes);

        } elseif ($element instanceof ConstantReflectionInterface) {
            return $this->createForConstant($element, $classes);

        } elseif ($element instanceof FunctionReflectionInterface) {
            return $this->createForFunction($element, $classes);
        }

        throw new UnexpectedValueException(
            'Descendant of ApiGen\Reflection\Reflection class expected. Got "'
            . get_class($element) . ' class".'
        );
    }


    /**
     * @return string
     */
    private function createForClass(ClassReflectionInterface $reflectionClass, array $classes)
    {
        return $this->linkBuilder->build(
            $this->elementUrlFactory->createForClass($reflectionClass),
            $reflectionClass->getName(),
            true,
            $classes
        );
    }


    /**
     * @return string
     */
    private function createForMethod(MethodReflectionInterface $reflectionMethod, array $classes)
    {
        return $this->linkBuilder->build(
            $this->elementUrlFactory->createForMethod($reflectionMethod),
            $reflectionMethod->getDeclaringClassName() . '::' . $reflectionMethod->getName() . '()',
            false,
            $classes
        );
    }


    /**
     * @return string
     */
    private function createForProperty(PropertyReflectionInterface $reflectionProperty, array $classes)
    {
        $text = $reflectionProperty->getDeclaringClassName() . '::' .
            Html::el('var')->setText('$' . $reflectionProperty->getName());

        return $this->linkBuilder->build(
            $this->elementUrlFactory->createForProperty($reflectionProperty),
            $text,
            false,
            $classes
        );
    }


    /**
     * @return string
     */
    private function createForConstant(ConstantReflectionInterface $reflectionConstant, array $classes)
    {
        $url = $this->elementUrlFactory->createForConstant($reflectionConstant);

        if ($reflectionConstant->getDeclaringClassName()) {
            $text = $reflectionConstant->getDeclaringClassName() . '::' .
                Html::el('b')->setText($reflectionConstant->getName());

        } else {
            $text = $this->getGlobalConstantName($reflectionConstant);
        }

        return $this->linkBuilder->build($url, $text, false, $classes);
    }


    /**
     * @return string
     */
    private function createForFunction(FunctionReflectionInterface $reflectionFunction, array $classes)
    {
        return $this->linkBuilder->build(
            $this->elementUrlFactory->createForFunction($reflectionFunction),
            $reflectionFunction->getName() . '()',
            true,
            $classes
        );
    }


    /**
     * @return string
     */
    private function getGlobalConstantName(ConstantReflectionInterface $reflectionConstant)
    {
        if ($reflectionConstant->inNamespace()) {
            return $reflectionConstant->getNamespaceName() . '\\' .
                Html::el('b')->setText($reflectionConstant->getShortName());

        } else {
            return Html::el('b')->setText($reflectionConstant->getName());
        }
    }
}
