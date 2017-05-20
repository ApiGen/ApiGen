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
            'subNamespace' => function (string $namespace): string {
                $namespaceSeparatorPosition = strrpos($namespace, self::NAMESPACE_SEPARATOR);
                if ($namespaceSeparatorPosition) {
                    return substr($namespace, $namespaceSeparatorPosition + 1);
                }

                return $namespace;
            },

            'linkAllNamespaceParts' => function (string $namespace): string {
//    private function namespaceLinks(string $namespace): string
//    {
//        $links = [];
//        $parent = '';
//        foreach (explode('\\', $namespace) as $part) {
//            $parent = ltrim($parent . '\\' . $part, '\\');
//            $links[] = $parent !== $namespace
//                ? $this->linkBuilder->build($this->namespaceUrl($parent), $part)
//                : $part;
//        }
//
//        return implode('\\', $links);
//    }
            }

//    public function testNamespaceLinks(): void
//    {
//        $this->assertSame(
//            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long.Namespace.html">Namespace</a>',
//            $this->namespaceUrlFilters->namespaceLinks('Long\Namespace')
//        );
//    }
//
//    public function testNamespaceLinksWithNoNamespaces(): void
//    {
//        $this->assertSame(
//            '<a href="namespace-Long.html">Long</a>\<a href="namespace-Long.Namespace.html">Namespace</a>',
//            $this->namespaceUrlFilters->namespaceLinks('Long\\Namespace')
//        );
//    }
//}
        ];
    }
}
