<?php declare(strict_types=1);


namespace ApiGen\Element\Latte\Filter;

use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class NamespaceFilter implements LatteFiltersProviderInterface
{
    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long.Namespace.html">Namespace</a>'
        ];
    }
}
