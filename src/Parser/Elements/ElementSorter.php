<?php declare(strict_types=1);

namespace ApiGen\Parser\Elements;

use ApiGen\Contracts\Parser\Elements\ElementSorterInterface;
use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionInterface;

final class ElementSorter implements ElementSorterInterface
{
    /**
     * @param mixed[] $elements
     * @return ConstantReflectionInterface[]|FunctionReflectionInterface[]|MethodReflectionInterface[]
     */
    public function sortElementsByFqn(array $elements): array
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
    private function sortConstantsByFqn(array $constantReflections): array
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
    private function sortFunctionsByFqn(array $functionReflections): array
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
    private function sortPropertiesOrMethodsByFqn(array $elementReflections): array
    {
        usort($elementReflections, function ($a, $b) {
            return $this->compareMethodsOrPropertiesByFqn($a, $b);
        });
        return $elementReflections;
    }

    private function compareConstantsByFqn(
        ConstantReflectionInterface $reflection1,
        ConstantReflectionInterface $reflection2
    ): int {
        return strcasecmp($this->getConstantFqnName($reflection1), $this->getConstantFqnName($reflection2));
    }

    private function getConstantFqnName(ConstantReflectionInterface $reflection): string
    {
        $class = $reflection->getDeclaringClassName();
        return $class . '\\' . $reflection->getName();
    }

    private function compareFunctionsByFqn(
        FunctionReflectionInterface $reflection1,
        FunctionReflectionInterface $reflection2
    ): int {
        return strcasecmp($this->getFunctionFqnName($reflection1), $this->getFunctionFqnName($reflection2));
    }

    private function getFunctionFqnName(FunctionReflectionInterface $reflection): string
    {
        return $reflection->getNamespaceName() . '\\' . $reflection->getName();
    }

    private function compareMethodsOrPropertiesByFqn(InClassInterface $reflection1, InClassInterface $reflection2): int
    {
        return strcasecmp(
            $this->getPropertyOrMethodFqnName($reflection1),
            $this->getPropertyOrMethodFqnName($reflection2)
        );
    }

    /**
     * @param InClassInterface|ReflectionInterface $reflection
     */
    private function getPropertyOrMethodFqnName(ReflectionInterface $reflection): string
    {
        return $reflection->getDeclaringClassName() . '::' . $reflection->getName();
    }
}
