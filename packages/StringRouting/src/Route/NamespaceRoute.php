<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Route;

use ApiGen\StringRouting\Contract\Route\RouteInterface;
use Nette\Utils\Strings;

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
    public function constructUrl($argument): string
    {
        return 'namespace-' . Strings::webalize($argument, 'a-z0-9', false) . '.html';
    }

//            'namespaceLinks' => function (string $namespace): string {
//                return $this->namespaceLinks($namespace);
//            },
//
//            'namespaceLinksWithoutLast' => function (string $namespace): string {
//                return $this->namespaceLinks($namespace, true);
//            }
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
}
