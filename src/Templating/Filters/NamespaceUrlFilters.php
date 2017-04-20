<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;

final class NamespaceUrlFilters extends Filters
{
    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    /**
     * @var ElementStorage
     */
    private $elementStorage;

    public function __construct(LinkBuilder $linkBuilder, ElementStorage $elementStorage)
    {
        $this->linkBuilder = $linkBuilder;
        $this->elementStorage = $elementStorage;
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
        if (! $this->elementStorage->getNamespaces()) {
            return $namespace;
        }

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

    public function namespaceUrl(string $name): string
    {
        return sprintf('namespace-%s.html', Filters::urlize($name));
    }
}
