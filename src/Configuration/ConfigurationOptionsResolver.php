<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\ModularConfiguration\Option\AnnotationGroupsOption;
use ApiGen\ModularConfiguration\Option\BaseUrlOption;
use ApiGen\ModularConfiguration\Option\ConfigurationFileOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\ExcludeOption;
use ApiGen\ModularConfiguration\Option\ExtensionsOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\ModularConfiguration\Option\ThemeDirectoryOption;
use ApiGen\ModularConfiguration\Option\VisibilityLevelOption;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigurationOptionsResolver
{
    /**
     * @var mixed[]
     */
    private $defaults = [
        // template parameters
        ConfigurationOptions::TITLE => '',
        ConfigurationOptions::GOOGLE_ANALYTICS => '',
        ConfigurationOptions::FORCE_OVERWRITE => false,
    ];

    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * @var DestinationOption
     */
    private $destinationOption;

    /**
     * @var ConfigurationFileOption
     */
    private $configurationFileOption;

    /**
     * @var AnnotationGroupsOption
     */
    private $annotationGroupsOption;

    /**
     * @var BaseUrlOption
     */
    private $baseUrlOption;

    /**
     * @var SourceOption
     */
    private $sourceOption;

    /**
     * @var ExcludeOption
     */
    private $excludeOption;

    /**
     * @var ThemeDirectoryOption
     */
    private $themeDirectoryOption;

    /**
     * @var ExtensionsOption
     */
    private $extensionsOption;

    /**
     * @var VisibilityLevelOption
     */
    private $visibilityLevelOption;

    public function __construct(
        DestinationOption $destinationOption,
        ConfigurationFileOption $configurationFileOption,
        AnnotationGroupsOption $annotationGroupsOption,
        BaseUrlOption $baseUrlOption,
        SourceOption $sourceOption,
        ExcludeOption $excludeOption,
        ThemeDirectoryOption $themeDirectoryOption,
        ExtensionsOption $extensionsOption,
        VisibilityLevelOption $visibilityLevelOption
    ) {
        $this->destinationOption = $destinationOption;
        $this->configurationFileOption = $configurationFileOption;
        $this->annotationGroupsOption = $annotationGroupsOption;
        $this->baseUrlOption = $baseUrlOption;
        $this->sourceOption = $sourceOption;
        $this->excludeOption = $excludeOption;
        $this->themeDirectoryOption = $themeDirectoryOption;
        $this->extensionsOption = $extensionsOption;
        $this->visibilityLevelOption = $visibilityLevelOption;
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function resolve(array $options): array
    {
        $this->resolver = new OptionsResolver();
        $this->resolver->setDefaults($this->defaults);

        // temp code
        if (!isset($options['destination'])) {
            throw new ConfigurationException;
        }

        $destination = $options['destination'];
        unset($options['destination']);

        $config = $options['config'] ?? '';
        unset($options['config']);

        $annotationGroups = $options['annotationGroups'] ?? '';
        unset($options['annotationGroups']);

        $baseUrl = $options['baseUrl'] ?? '';
        unset($options['baseUrl']);

        $extensions = $options['extensions'] ?? [];
        unset($options['extensions']);

        $exclude = $options['exclude'] ?? [];
        unset($options['exclude']);

        $visibilityLevel = $options['visibilityLevels'] ?? [];
        unset($options['visibilityLevels']);

        $themeDirectory = $options['themeDirectory'] ?? null;
        unset($options['themeDirectory']);

        if (!isset($options['source'])) {
            throw new ConfigurationException;
        }

        $source = $options['source'];
        unset($options['source']);

        $options = $this->resolver->resolve($options);

        $options['destination'] = $this->destinationOption->resolveValue($destination);
        $options['config'] = $this->configurationFileOption->resolveValue($config);
        $options['annotationGroups'] = $this->annotationGroupsOption->resolveValue($annotationGroups);
        $options['baseUrl'] = $this->baseUrlOption->resolveValue($baseUrl);
        $options['source'] = $this->sourceOption->resolveValue($source);
        $options['exclude'] = $this->excludeOption->resolveValue($exclude);
        $options['themeDirectory'] = $this->themeDirectoryOption->resolveValue($themeDirectory);
        $options['extensions'] = $this->extensionsOption->resolveValue($extensions);
        $options['visibilityLevels'] = $this->visibilityLevelOption->resolveValue($visibilityLevel);

        return $options;
    }
}
