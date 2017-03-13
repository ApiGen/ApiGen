<?php

namespace ApiGen\Templating\Filters;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;

class NamespaceAndPackageUrlFilters extends Filters
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


    /**
     * @param string $groupName
     * @return string
     */
    public function subgroupName($groupName)
    {
        if ($pos = strrpos($groupName, '\\')) {
            return substr($groupName, $pos + 1);
        }
        return $groupName;
    }


    /**
     * @param string $namespace
     * @param bool $skipLast
     * @return string
     */
    public function namespaceLinks($namespace, $skipLast = true)
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


    /**
     * @param string $name
     * @return string
     */
    public function namespaceUrl($name)
    {
        return sprintf(
            $this->configuration->getOption(CO::TEMPLATE)['templates']['namespace']['filename'],
            $this->urlize($name)
        );
    }
}
