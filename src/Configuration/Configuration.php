<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\ModularConfiguration\Option\AnnotationGroupsOption;
use ApiGen\ModularConfiguration\Option\BaseUrlOption;
use ApiGen\ModularConfiguration\Option\ConfigurationFileOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\ExcludeOption;
use ApiGen\ModularConfiguration\Option\ExtensionsOption;
use ApiGen\ModularConfiguration\Option\GoogleAnalyticsOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\ModularConfiguration\Option\ThemeDirectoryOption;
use ApiGen\ModularConfiguration\Option\TitleOption;
use ApiGen\ModularConfiguration\Option\VisibilityLevelOption;
use ApiGen\Templating\Filters\UrlFilters;
use ApiGen\Utils\FileSystem;
use Nette\DI\Config\Loader;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var mixed[]
     */
    private $options;

    /**
     * @var ConfigurationOptionsResolver
     */
    private $configurationOptionsResolver;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(
        ConfigurationOptionsResolver $configurationOptionsResolver,
        FileSystem $fileSystem
    ) {
        $this->configurationOptionsResolver = $configurationOptionsResolver;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function resolveOptions(array $options): array
    {
        return $this->options = $this->configurationOptionsResolver->resolve($options);
    }

    /**
     * @return mixed|null
     */
    public function getOption(string $name)
    {
        if (isset($this->getOptions()[$name])) {
            return $this->getOptions()[$name];
        }

        return null;
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

    /**
     * @param mixed[] $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
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

    public function getGoogleAnalytics(): string
    {
        return $this->getOptions()[GoogleAnalyticsOption::NAME];
    }

    /**
     * @return string[]
     */
    public function getSource(): array
    {
        return $this->getOptions()[SourceOption::NAME];
    }

    /**
     * @return string[]
     */
    public function getExclude(): array
    {
        return $this->getOptions()[ExcludeOption::NAME];
    }

    /**
     * @return string[]
     */
    public function getExtensions(): array
    {
        return $this->getOptions()[ExtensionsOption::NAME];
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
        return $this->getDestination() . DIRECTORY_SEPARATOR . sprintf(
            $prefix . '%s.html',
            UrlFilters::urlize($name)
        );
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function prepareOptions(array $options): array
    {
        $options = $this->loadOptionsFromConfig($options);

        return $this->resolveOptions($options);
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    private function loadOptionsFromConfig(array $options): array
    {
        $configFile = $options[ConfigurationFileOption::NAME] ?? getcwd() . DIRECTORY_SEPARATOR . 'apigen.neon';
        $configFile = $this->fileSystem->getAbsolutePath($configFile);

        if (file_exists($configFile)) {
            $configFileOptions = (new Loader)->load($configFile);
            return array_merge($options, $configFileOptions);
        }

        return $options;
    }
}
