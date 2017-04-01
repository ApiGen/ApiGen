<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;

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

    public function __construct(ConfigurationOptionsResolver $configurationOptionsResolver)
    {
        $this->configurationOptionsResolver = $configurationOptionsResolver;
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function resolveOptions(array $options): array
    {
        $options = $this->unsetConsoleOptions($options);
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

    public function getVisibilityLevel(): int
    {
        return $this->options[ConfigurationOptions::VISIBILITY_LEVELS];
    }

    /**
     * @return string[]
     */
    public function getAnnotationGroups(): array
    {
        return $this->options[ConfigurationOptions::ANNOTATION_GROUPS];
    }

    public function getDestination(): string
    {
        return $this->options[ConfigurationOptions::DESTINATION];
    }

    public function getTitle(): string
    {
        return $this->options[ConfigurationOptions::TITLE];
    }

    public function getBaseUrl(): string
    {
        return $this->options[ConfigurationOptions::BASE_URL];
    }

    public function getGoogleAnalytics(): string
    {
        return $this->options[ConfigurationOptions::GOOGLE_ANALYTICS];
    }

    /**
     * @return array|string[]
     */
    public function getSource(): array
    {
        return $this->options[ConfigurationOptions::SOURCE];
    }

    /**
     * @return array|string[]
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

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    private function unsetConsoleOptions(array $options): array
    {
        unset(
            $options['ansi'], $options['no-ansi'], $options['no-interaction'], $options['config'],
            $options['help'], $options['quiet'], $options['verbose'], $options['version']
        );
        return $options;
    }
}
