<?php declare(strict_types=1);

namespace ApiGen\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Parser\Reflection\ReflectionElement;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;

class NamespaceUrlFilters extends Filters
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var LinkBuilder
     */
    private $linkBuilder;

    /**
     * @var ElementStorage
     */
    private $elementStorage;

    public function __construct(Configuration $configuration, LinkBuilder $linkBuilder, ElementStorage $elementStorage)
    {
        $this->configuration = $configuration;
        $this->linkBuilder = $linkBuilder;
        $this->elementStorage = $elementStorage;
    }

    public function subgroupName(string $groupName): string
    {
        if ($pos = strrpos($groupName, '\\')) {
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
        return sprintf(
            $this->configuration->getOption(ConfigurationOptions::TEMPLATE)['templates']['namespace']['filename'],
            $this->urlize($name)
        );
    }
}
