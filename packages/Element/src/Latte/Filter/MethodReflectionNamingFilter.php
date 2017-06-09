<?php declare(strict_types=1);

namespace ApiGen\Element\Latte\Filter;

use ApiGen\Contract\Templating\FilterProviderInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;

final class MethodReflectionNamingFilter implements FilterProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            // use in .latte: {$method|prettyMethodName}
            'prettyMethodName' => function ($methodReflection) {
                if ($methodReflection instanceof ClassMethodReflectionInterface) {
                    return $methodReflection->getDeclaringClassName() . '::' . $methodReflection->getName() . '()';
                }

                if ($methodReflection instanceof InterfaceMethodReflectionInterface) {
                    return $methodReflection->getDeclaringInterfaceName() . '::' . $methodReflection->getName() . '()';
                }

                if ($methodReflection instanceof TraitMethodReflectionInterface) {
                    return $methodReflection->getDeclaringTraitName() . '::' . $methodReflection->getName() . '()';
                }
            }
        ];
    }
}
