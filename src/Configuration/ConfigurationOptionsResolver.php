<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\Configuration\Theme\ThemeConfigFactory;
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
        // required
        ConfigurationOptions::SOURCE => [],
        ConfigurationOptions::DESTINATION => null,
        // file finder
        ConfigurationOptions::EXCLUDE => [],
        ConfigurationOptions::EXTENSIONS => ['php'],
        // template parameters
        ConfigurationOptions::TITLE => '',
        ConfigurationOptions::GOOGLE_ANALYTICS => '',
        // filtering generated content
        ConfigurationOptions::VISIBILITY_LEVELS => [self::VISIBILITY_LEVEL_PUBLIC, self::VISIBILITY_LEVEL_PROTECTED],
        ConfigurationOptions::ANNOTATION_GROUPS => [],
        ConfigurationOptions::BASE_URL => '',
        ConfigurationOptions::CONFIG => '',
        ConfigurationOptions::FORCE_OVERWRITE => false,
        ConfigurationOptions::TEMPLATE_CONFIG => null
    ];

    /**
     * @var ThemeConfigFactory
     */
    private $themeConfigFactory;

    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * @var OptionsResolverFactory
     */
    private $optionsResolverFactory;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(
        ThemeConfigFactory $themeConfigFactory,
        OptionsResolverFactory $optionsResolverFactory,
        FileSystem $fileSystem
    ) {
        $this->themeConfigFactory = $themeConfigFactory;
        $this->optionsResolverFactory = $optionsResolverFactory;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param mixed[] $options
     * @return mixed[]
     */
    public function resolve(array $options): array
    {
        $this->resolver = $this->optionsResolverFactory->create();
        $this->setDefaults();
        $this->setRequired();
        $this->setAllowedValues();
        $this->setNormalizers();

        return $this->resolver->resolve($options);
    }

    private function setDefaults(): void
    {
        $this->resolver->setDefaults($this->defaults);
        $this->resolver->setDefaults([
            ConfigurationOptions::TEMPLATE => function (Options $options) {
                $config = $options[ConfigurationOptions::TEMPLATE_CONFIG];
                if ($config === '') {
                    $config = getcwd() . '/packages/ThemeDefault/src/config.neon';
                }

                return $this->themeConfigFactory->create($config)
                    ->getOptions();
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

    private function setRequired(): void
    {
        $this->resolver->setRequired([ConfigurationOptions::SOURCE, ConfigurationOptions::DESTINATION]);
    }

    private function setAllowedValues(): void
    {
        $this->resolver->addAllowedValues(ConfigurationOptions::DESTINATION, function ($destination) {
            return $this->allowedValuesForDestination($destination);
        });

        $this->resolver->addAllowedValues(ConfigurationOptions::SOURCE, function ($source) {
            return $this->allowedValuesForSource($source);
        });

        $this->resolver->addAllowedValues(ConfigurationOptions::TEMPLATE_CONFIG, function ($value) {
            if ($value && ! is_file($value)) {
                throw new ConfigurationException(sprintf(
                    'Template config "%s" was not found.', $value
                ));
            }

            return true;
        });
    }

    private function setNormalizers(): void
    {
        $this->resolver->setNormalizer(ConfigurationOptions::ANNOTATION_GROUPS, function (Options $options, $value) {
            if ($value === '') {
                return [];
            }

            return $value;
        });

        $this->resolver->setNormalizer(ConfigurationOptions::DESTINATION, function (Options $options, $value) {
            return $this->fileSystem->getAbsolutePath($value);
        });

        $this->resolver->setNormalizer(ConfigurationOptions::BASE_URL, function (Options $options, $value) {
            return rtrim((string) $value, '/');
        });

        $this->resolver->setNormalizer(ConfigurationOptions::SOURCE, function (Options $options, $value) {
            if (! is_array($value)) {
                $value = [$value];
            }

            foreach ($value as $key => $source) {
                $value[$key] = $this->fileSystem->getAbsolutePath($source);
            }

            return $value;
        });

        $this->resolver->setNormalizer(ConfigurationOptions::TEMPLATE_CONFIG, function (Options $options, $value) {
            if ($value === null) {
                return '';
            }

            return $this->fileSystem->getAbsolutePath($value);
        });

        $this->resolver->setNormalizer(ConfigurationOptions::VISIBILITY_LEVELS, function (Options $options, $value) {
            return $this->normalizeVisibilityLevelsToBinary($value);
        });
    }

    private function allowedValuesForDestination(?string $destination): bool
    {
        if (! $destination) {
            throw new ConfigurationException(
                'Destination is not set. Use "--destination <directory>" or config to set it.'
            );
        }

        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        if (! is_writable($destination)) {
            throw new ConfigurationException(sprintf(
                'Destination "%s" is not writable.',
                $destination
            ));
        }

        return true;
    }

    /**
     * @param string[] $source
     */
    private function allowedValuesForSource(array $source): bool
    {
        foreach ($source as $singleSource) {
            $this->ensureSourceExists($singleSource);
        }

        return true;
    }

    private function ensureSourceExists(string $singleSource): void
    {
        if (! file_exists($singleSource)) {
            throw new ConfigurationException(sprintf(
                'Source "%s" does not exist',
                $singleSource
            ));
        }
    }
}
