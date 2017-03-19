<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\Contracts\Configuration\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @var array
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


    public function resolveOptions(array $options): array
    {
        $options = $this->unsetConsoleOptions($options);
        return $this->options = $this->configurationOptionsResolver->resolve($options);
    }


    public function getOption(string $name)
    {
        if (isset($this->getOptions()[$name])) {
            return $this->getOptions()[$name];
        }

        return null;
    }


    public function getOptions(): array
    {
        if ($this->options === null) {
            $this->resolveOptions([]);
        }

        return $this->options;
    }


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


    public function shouldGenerateSourceCode(): bool
    {
        return $this->options['sourceCode'];
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
     * @return array|string[]
     */
    public function getExtensions(): array
    {
        return $this->options['extensions'];
    }


    private function unsetConsoleOptions(array $options): array
    {
        unset($options['config'], $options['help'], $options['quiet']);
        return $options;
    }
}
