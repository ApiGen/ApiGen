<?php declare(strict_types=1);

namespace ApiGen\Element\Latte\Filter;

use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Element\Naming\ReflectionNaming;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class MethodReflectionNamingFilter implements LatteFiltersProviderInterface
{
    /**
     * @var ReflectionNaming
     */
    private $reflectionNaming;

    public function __construct(ReflectionNaming $reflectionNaming)
    {
        $this->reflectionNaming = $reflectionNaming;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'prettyMethodName' => function (MethodReflectionInterface $methodReflection) {
                return $this->reflectionNaming->forMethodReflection($methodReflection);
            }
        ];
    }
}
