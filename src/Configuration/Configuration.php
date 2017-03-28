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
        return $this->options['visibilityLevels'];
    }

    public function getMain(): string
    {
        return $this->getOption('main');
    }

    public function isInternalDocumented(): bool
    {
        return (bool) $this->getOption('internal');
    }

    /**
     * @return string[]
     */
    public function getAnnotationGroups(): array
    {
        return $this->options['annotationGroups'];
    }

    public function getDestination(): string
    {
        return $this->options['destination'];
    }

    public function getTitle(): string
    {
        return $this->options['title'];
    }

    public function getBaseUrl(): string
    {
        return $this->options['baseUrl'];
    }

    public function getGoogleCseId(): string
    {
        return $this->options['googleCseId'];
    }

    /**
     * @return array|string[]
     */
    public function getSource(): array
    {
        return $this->options['source'];
    }

    /**
     * @return array|string[]
     */
    public function getExclude(): array
    {
        return $this->options['exclude'];
    }

    /**
     * @return string[]
     */
    public function getExtensions(): array
    {
        return $this->options['extensions'];
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    private function unsetConsoleOptions(array $options): array
    {
        unset(
            $options['ansi'], $options['noAnsi'], $options['noInteraction'], $options['config'],
            $options['help'], $options['quiet'], $options['verbose'], $options['version']
        );
        return $options;
    }
}
