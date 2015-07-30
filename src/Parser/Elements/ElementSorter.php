<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementSorterInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

class ElementSorter implements ElementSorterInterface
{

    /**
     * {@inheritdoc}
     */
    public function sortElementsByFqn(array $elements)
    {
        if (count($elements)) {
            $firstElement = array_values($elements)[0];
            if ($firstElement instanceof ConstantReflectionInterface) {
                return $this->sortConstantsByFqn($elements);

            } elseif ($firstElement instanceof FunctionReflectionInterface) {
                return $this->sortFunctionsByFqn($elements);

            } elseif ($firstElement instanceof InClassInterface) {
                return $this->sortPropertiesOrMethodsByFqn($elements);
            }
        }
        return $elements;
    }


    /**
     * @param ConstantReflectionInterface[] $constantReflections
     * @return ConstantReflectionInterface[]
     */
    private function sortConstantsByFqn($constantReflections)
    {
        usort($constantReflections, function ($a, $b) {
            return $this->compareConstantsByFqn($a, $b);
        });
        return $constantReflections;
    }


    /**
     * @param FunctionReflectionInterface[] $functionReflections
     * @return FunctionReflectionInterface[]
     */
    private function sortFunctionsByFqn($functionReflections)
    {
        usort($functionReflections, function ($a, $b) {
            return $this->compareFunctionsByFqn($a, $b);
        });
        return $functionReflections;
    }


    /**
     * @param InClassInterface[] $elementReflections
     * @return MethodReflectionInterface[]
     */
    private function sortPropertiesOrMethodsByFqn($elementReflections)
    {
        usort($elementReflections, function ($a, $b) {
            return $this->compareMethodsOrPropertiesByFqn($a, $b);
        });
        return $elementReflections;
    }


    /**
     * @return int
     */
    private function compareConstantsByFqn(
        ConstantReflectionInterface $reflection1,
        ConstantReflectionInterface $reflection2
    ) {
        return strcasecmp($this->getConstantFqnName($reflection1), $this->getConstantFqnName($reflection2));
    }


    /**
     * @return string
     */
    private function getConstantFqnName(ConstantReflectionInterface $reflection)
    {
        $class = $reflection->getDeclaringClassName() ?: $reflection->getNamespaceName();
        return $class . '\\' . $reflection->getName();
    }


    /**
     * @return int
     */
    private function compareFunctionsByFqn(
        FunctionReflectionInterface $reflection1,
        FunctionReflectionInterface $reflection2
    ) {
        return strcasecmp($this->getFunctionFqnName($reflection1), $this->getFunctionFqnName($reflection2));
    }


    /**
     * @return string
     */
    private function getFunctionFqnName(FunctionReflectionInterface $reflection)
    {
        return $reflection->getNamespaceName() . '\\' . $reflection->getName();
    }


    /**
     * @return int
     */
    private function compareMethodsOrPropertiesByFqn(InClassInterface $reflection1, InClassInterface $reflection2)
    {
        return strcasecmp(
            $this->getPropertyOrMethodFqnName($reflection1),
            $this->getPropertyOrMethodFqnName($reflection2)
        );
    }


    /**
     * @return string
     */
    private function getPropertyOrMethodFqnName(InClassInterface $reflection)
    {
        return $reflection->getDeclaringClassName() . '::' . $reflection->getName();
    }
}
