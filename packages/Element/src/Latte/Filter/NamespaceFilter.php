<?php declare(strict_types=1);


namespace ApiGen\Element\Latte\Filter;

use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;


final class NamespaceFilter implements LatteFiltersProviderInterface
{
    /**
     * @var string
     */
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'subNamespaceName' => function (string $namespaceName): string {
                $namespaceSeparatorPosition = strrpos($namespaceName, self::NAMESPACE_SEPARATOR);
                if ($namespaceSeparatorPosition) {
                    return substr($namespaceName, $namespaceSeparatorPosition + 1);
                }

                return $namespaceName;
            },
        ];
    }
}
