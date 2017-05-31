<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\ModularConfiguration\Contract\ConfigurationResolverInterface;
use ApiGen\ModularConfiguration\Option\AnnotationGroupsOption;
use ApiGen\ModularConfiguration\Option\BaseUrlOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\ModularConfiguration\Option\ThemeDirectoryOption;
use ApiGen\ModularConfiguration\Option\TitleOption;
use ApiGen\ModularConfiguration\Option\VisibilityLevelOption;
use ApiGen\ModularConfiguration\Parameter\ParameterProvider;
use ApiGen\Utils\NamingHelper;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var mixed[]
     */
    private $options;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @var ConfigurationResolverInterface
     */
    private $configurationResolver;

    public function __construct(
        ParameterProvider $parameterProvider,
        ConfigurationResolverInterface $configurationResolver
    ) {
        $this->parameterProvider = $parameterProvider;
        $this->configurationResolver = $configurationResolver;
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function resolveOptions(array $options): array
    {
        $configParameters = $this->parameterProvider->provide();
        $options = array_merge($configParameters, $options);

        return $this->options = $this->configurationResolver->resolveValuesWithDefaults($options);
    }

    /**
     * @return mixed|null
     */
    public function getOption(string $name)
    {
        return $this->getOptions()[$name] ?? null;
    }

    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        if ($this->options === null) {
            $this->resolveOptions([]);
        }

        return $this->options;
    }

    public function getVisibilityLevels(): int
    {
        return $this->getOptions()[VisibilityLevelOption::NAME];
    }

    /**
     * @return string[]
     */
    public function getAnnotationGroups(): array
    {
        return $this->getOptions()[AnnotationGroupsOption::NAME];
    }

    public function getDestination(): string
    {
        return $this->getOptions()[DestinationOption::NAME];
    }

    public function getTitle(): string
    {
        return $this->getOptions()[TitleOption::NAME];
    }

    public function getBaseUrl(): string
    {
        return $this->getOptions()[BaseUrlOption::NAME];
    }

    /**
     * @return string[]
     */
    public function getSource(): array
    {
        return $this->getOptions()[SourceOption::NAME];
    }

    public function getTemplatesDirectory(): string
    {
        return $this->getOptions()[ThemeDirectoryOption::NAME];
    }

    public function getTemplateByName(string $name): string
    {
        return $this->getTemplatesDirectory() . DIRECTORY_SEPARATOR . $name . '.latte';
    }

    public function getDestinationWithName(string $name): string
    {
        return $this->getDestination() . DIRECTORY_SEPARATOR . $name . '.html';
    }

    public function getDestinationWithPrefixName(string $prefix, string $name): string
    {
        return $this->getDestination() . DIRECTORY_SEPARATOR .
            $prefix . NamingHelper::nameToFilePath($name) . '.html';
    }
}
