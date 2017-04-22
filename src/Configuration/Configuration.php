<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
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
        return $this->getOptions()[ConfigurationOptions::VISIBILITY_LEVELS];
    }

    /**
     * @return string[]
     */
    public function getAnnotationGroups(): array
    {
        return $this->getOptions()[ConfigurationOptions::ANNOTATION_GROUPS];
    }

    public function getDestination(): string
    {
        return $this->getOptions()[ConfigurationOptions::DESTINATION];
    }

    public function getTitle(): string
    {
        return $this->getOptions()[ConfigurationOptions::TITLE];
    }

    public function getBaseUrl(): string
    {
        return $this->getOptions()[ConfigurationOptions::BASE_URL];
    }

    public function getGoogleAnalytics(): string
    {
        return $this->getOptions()[ConfigurationOptions::GOOGLE_ANALYTICS];
    }

    /**
     * @return string[]
     */
    public function getSource(): array
    {
        return $this->getOptions()[ConfigurationOptions::SOURCE];
    }

    /**
     * @return string[]
     */
    public function getExclude(): array
    {
        return $this->getOptions()[ConfigurationOptions::EXCLUDE];
    }

    /**
     * @return string[]
     */
    public function getExtensions(): array
    {
        return $this->getOptions()[ConfigurationOptions::EXTENSIONS];
    }

    public function getTemplatesDirectory(): string
    {
        return $this->getOptions()[ConfigurationOptions::THEME_DIRECTORY];
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
        $configFile = $options[ConfigurationOptions::CONFIG] ?? getcwd() . DIRECTORY_SEPARATOR . 'apigen.neon';
        $configFile = $this->fileSystem->getAbsolutePath($configFile);

        if (file_exists($configFile)) {
            $configFileOptions = (new Loader)->load($configFile);
            return array_merge($options, $configFileOptions);
        }

        return $options;
    }
}
