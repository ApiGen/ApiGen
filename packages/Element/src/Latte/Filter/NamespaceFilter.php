<?php declare(strict_types=1);


namespace ApiGen\Element\Latte\Filter;

use ApiGen\StringRouting\Route\NamespaceRoute;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class NamespaceFilter implements LatteFiltersProviderInterface
{
    /**
     * @var string
     */
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @var NamespaceRoute
     */
    private $namespaceRoute;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    public function __construct(NamespaceRoute $namespaceRoute, LinkBuilder $linkBuilder)
    {
        $this->namespaceRoute = $namespaceRoute;
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'subNamespace' => function (string $namespace): string {
                $namespaceSeparatorPosition = strrpos($namespace, self::NAMESPACE_SEPARATOR);
                if ($namespaceSeparatorPosition) {
                    return substr($namespace, $namespaceSeparatorPosition + 1);
                }

                return $namespace;
            },
            'linkAllNamespaceParts' => function (string $namespace): string {
                $links = [];
                $parent = '';
                foreach (explode('\\', $namespace) as $part) {
                    $parent = ltrim($parent . '\\' . $part, '\\');
                    $links[] = $this->linkBuilder->build($this->namespaceRoute->constructUrl($parent), $part);
                }

                return implode('\\', $links);
            }
        ];
    }
}
