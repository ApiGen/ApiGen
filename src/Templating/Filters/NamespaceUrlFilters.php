<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use Symplify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class NamespaceUrlFilters implements LatteFiltersProviderInterface
{
    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    public function __construct(LinkBuilder $linkBuilder)
    {
        $this->linkBuilder = $linkBuilder;
    }

    /**
     * @return callable[]
     */
    public function getFilters(): array
    {
        return [
            'subNamespaceName' => function (string $namespaceName): string {
                $namespaceSeparatorPosition = strrpos($namespaceName, '\\');
                if ($namespaceSeparatorPosition) {
                    return substr($namespaceName, $namespaceSeparatorPosition + 1);
                }

                return $namespaceName;
            },

            'namespaceUrl' => function (string $name): string {
                return $this->namespaceUrl($name);
            },

            'namespaceLinks' => function (string $namespace): string {
                return $this->namespaceLinks($namespace);
            },

            'namespaceLinksWithoutLast' => function (string $namespace): string {
                return $this->namespaceLinks($namespace, true);
            }
        ];
    }

    private function namespaceLinks(string $namespace, bool $skipLast = true): string
    {
        $links = [];

        $parent = '';
        foreach (explode('\\', $namespace) as $part) {
            $parent = ltrim($parent . '\\' . $part, '\\');
            $links[] = $skipLast || $parent !== $namespace
                ? $this->linkBuilder->build($this->namespaceUrl($parent), $part)
                : $part;
        }

        return implode('\\', $links);
    }

    private function namespaceUrl(string $name): string
    {
        return sprintf('namespace-%s.html', Filters::urlize($name));
    }
}
