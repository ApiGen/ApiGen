<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Templating\Filters\Helpers\LinkBuilder;

final class NamespaceUrlFilters extends Filters
{
    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    public function __construct(LinkBuilder $linkBuilder)
    {
        $this->linkBuilder = $linkBuilder;
    }

    public function subgroupName(string $groupName): string
    {
        $pos = strrpos($groupName, '\\');

        if ($pos) {
            return substr($groupName, $pos + 1);
        }

        return $groupName;
    }

    public function namespaceLinks(string $namespace, bool $skipLast = true): string
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

    // @todo
    // public function namespaceLinkWithoutLast(string $namespace): string

    public function namespaceUrl(string $name): string
    {
        return sprintf('namespace-%s.html', Filters::urlize($name));
    }
}
