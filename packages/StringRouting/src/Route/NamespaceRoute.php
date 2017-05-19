<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\StringRouting\Contract\Route\RouteInterface;

final class NamespaceRoute implements RouteInterface
{
    /**
     * @var string
     */
    public const NAME = 'namespace';

    public function match(string $name): bool
    {
        return $name === self::NAME;
    }

    /**
     * @param string $argument
     */
    public function constructUrl(string $argument): string
    {
        // TODO: Implement constructUrl() method.
    }

//
//    /**
//     * @return callable[]
//     */
//    public function getFilters(): array
//    {
//        return [
//            'subNamespaceName' => function (string $namespaceName): string {
//                $namespaceSeparatorPosition = strrpos($namespaceName, '\\');
//                if ($namespaceSeparatorPosition) {
//                    return substr($namespaceName, $namespaceSeparatorPosition + 1);
//                }
//
//                return $namespaceName;
//            },
//
//            'namespaceUrl' => function (string $name): string {
//                return $this->namespaceUrl($name);
//            },
//
//            'namespaceLinks' => function (string $namespace): string {
//                return $this->namespaceLinks($namespace);
//            },
//
//            'namespaceLinksWithoutLast' => function (string $namespace): string {
//                return $this->namespaceLinks($namespace, true);
//            }
//        ];
//    }
//
//    private function namespaceLinks(string $namespace, bool $skipLast = true): string
//    {
//        $links = [];
//
//        $parent = '';
//        foreach (explode('\\', $namespace) as $part) {
//            $parent = ltrim($parent . '\\' . $part, '\\');
//            $links[] = $skipLast || $parent !== $namespace
//                ? $this->linkBuilder->build($this->namespaceUrl($parent), $part)
//                : $part;
//        }
//
//        return implode('\\', $links);
//    }
//
//    private function namespaceUrl(string $name): string
//    {
//        return sprintf('namespace-%s.html', Filters::urlize($name));

//    }

}
