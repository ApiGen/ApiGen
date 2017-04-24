<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\ModularConfiguration\Option\AnnotationGroupsOption;
use ApiGen\ModularConfiguration\Option\BaseUrlOption;
use ApiGen\ModularConfiguration\Option\ConfigurationFileOption;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
use ApiGen\Utils\FileSystem;
use ReflectionProperty;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigurationOptionsResolver
{
    /**
     * @var string
     */
    public const VISIBILITY_LEVEL_PROTECTED = 'protected';

    /**
     * @var string
     */
    public const VISIBILITY_LEVEL_PRIVATE = 'private';

    /**
     * @var string
     */
    public const VISIBILITY_LEVEL_PUBLIC = 'public';

    /**
     * @var mixed[]
     */
    private $defaults = [
        // file finder
        ConfigurationOptions::EXCLUDE => [],
        ConfigurationOptions::EXTENSIONS => ['php'],
        // template parameters
        ConfigurationOptions::TITLE => '',
        ConfigurationOptions::GOOGLE_ANALYTICS => '',
        // filtering generated content
        ConfigurationOptions::VISIBILITY_LEVELS => [self::VISIBILITY_LEVEL_PUBLIC, self::VISIBILITY_LEVEL_PROTECTED],
        ConfigurationOptions::FORCE_OVERWRITE => false,
        ConfigurationOptions::THEME_DIRECTORY => null
    ];

    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * @var FileSystem
     */
    private $fileSystem;

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

    public function __construct(
        FileSystem $fileSystem,
        DestinationOption $destinationOption,
        ConfigurationFileOption $configurationFileOption,
        AnnotationGroupsOption $annotationGroupsOption,
        BaseUrlOption $baseUrlOption,
        SourceOption $sourceOption
    ) {
        $this->fileSystem = $fileSystem;
        $this->destinationOption = $destinationOption;
        $this->configurationFileOption = $configurationFileOption;
        $this->annotationGroupsOption = $annotationGroupsOption;
        $this->baseUrlOption = $baseUrlOption;
        $this->sourceOption = $sourceOption;
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function resolve(array $options): array
    {
        $this->resolver = new OptionsResolver();
        $this->setDefaults();
//        $this->setRequired();
        $this->setAllowedValues();
        $this->setNormalizers();

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

        return $options;
    }

    private function setDefaults(): void
    {
        $this->resolver->setDefaults($this->defaults);
        $this->resolver->setDefaults([
            ConfigurationOptions::THEME_DIRECTORY => function (Options $options, $value) {
                if ($value) {
                    return $value;
                }

                return getcwd() . '/packages/ThemeDefault/src';
            }
        ]);
    }

    /**
     * @param mixed[] $options
     */
    private function normalizeVisibilityLevelsToBinary(array $options): int
    {
        $visibilityLevelInInteger = 0;

        if (in_array(self::VISIBILITY_LEVEL_PUBLIC, $options)) {
            $visibilityLevelInInteger |= ReflectionProperty::IS_PUBLIC;
        }

        if (in_array(self::VISIBILITY_LEVEL_PROTECTED, $options)) {
            $visibilityLevelInInteger |= ReflectionProperty::IS_PROTECTED;
        }

        if (in_array(self::VISIBILITY_LEVEL_PRIVATE, $options)) {
            $visibilityLevelInInteger |= ReflectionProperty::IS_PRIVATE;
        }

        return $visibilityLevelInInteger;
    }

    private function setAllowedValues(): void
    {
        $this->resolver->addAllowedValues(ConfigurationOptions::THEME_DIRECTORY, function ($value) {
            if ($value && ! is_dir($value)) {
                throw new ConfigurationException(sprintf(
                    'Theme directory "%s" was not found.',
                    $value
                ));
            }

            return true;
        });
    }

    private function setNormalizers(): void
    {
        $this->resolver->setNormalizer(ConfigurationOptions::THEME_DIRECTORY, function (Options $options, $value) {
            if ($value === null) {
                return '';
            }

            return $this->fileSystem->getAbsolutePath($value);
        });

        $this->resolver->setNormalizer(ConfigurationOptions::VISIBILITY_LEVELS, function (Options $options, $value) {
            return $this->normalizeVisibilityLevelsToBinary($value);
        });
    }
}
