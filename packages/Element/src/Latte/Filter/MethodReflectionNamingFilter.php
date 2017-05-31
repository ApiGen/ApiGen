<?php declare(strict_types=1);

namespace ApiGen\Element\Latte\Filter;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class MethodReflectionNamingFilter implements LatteFiltersProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'prettyMethodName' => function (ClassMethodReflectionInterface $methodReflection) {
                return $methodReflection->getDeclaringClassName() . '::' . $methodReflection->getName() . '()';
            }
        ];
    }
}
