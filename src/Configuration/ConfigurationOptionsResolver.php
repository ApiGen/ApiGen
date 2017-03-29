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
    public const AL_PROTECTED = 'protected';

    /**
     * @var string
     */
    public const AL_PRIVATE = 'private';

    /**
     * @var string
     */
    public const AL_PUBLIC = 'public';

    /**
     * @var mixed[]
     */
    private $defaults = [
        ConfigurationOptions::ANNOTATION_GROUPS => [],
        ConfigurationOptions::ACCESS_LEVELS => ['public'],
        ConfigurationOptions::BASE_URL => '',
        ConfigurationOptions::CONFIG => '',
        ConfigurationOptions::DESTINATION => null,
        ConfigurationOptions::FORCE_OVERWRITE => false,
        ConfigurationOptions::EXCLUDE => [],
        ConfigurationOptions::EXTENSIONS => ['php'],
        ConfigurationOptions::GOOGLE_CSE_ID => '',
        ConfigurationOptions::GOOGLE_ANALYTICS => '',
        ConfigurationOptions::MAIN => '',
        ConfigurationOptions::SOURCE => [],
        ConfigurationOptions::TEMPLATE => null,
        ConfigurationOptions::TEMPLATE_CONFIG => null,
        ConfigurationOptions::TITLE => '',
        // helpers
        ConfigurationOptions::VISIBILITY_LEVELS => [],
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
            ConfigurationOptions::VISIBILITY_LEVELS => function (Options $options) {
                return $this->getAccessLevelForReflections($options[ConfigurationOptions::ACCESS_LEVELS]);
            },
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
     * @return int
     */
    private function getAccessLevelForReflections(array $options): int
    {
        $accessLevel = null;

        if (in_array(self::AL_PUBLIC, $options)) {
            $accessLevel |= ReflectionProperty::IS_PUBLIC;
        }

        if (in_array(self::AL_PROTECTED, $options)) {
            $accessLevel |= ReflectionProperty::IS_PROTECTED;
        }

        if (in_array(self::AL_PRIVATE, $options)) {
            $accessLevel |= ReflectionProperty::IS_PRIVATE;
        }

        return $accessLevel;
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
            $value = (array) $value;
            return array_unique($value);
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
    }

    private function allowedValuesForDestination(?string $destination): bool
    {
        if (! $destination) {
            throw new ConfigurationException("Destination is not set. Use '-d <dir>' or config to set it");
        } elseif (! is_dir($destination)) {
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
     * @param string|string[] $source
     * @return bool
     */
    private function allowedValuesForSource($source): bool
    {
        if (! $source) {
            throw new ConfigurationException("Source is not set. Use '-s <dir>' or config to set it");
        } elseif (! is_array($source)) {
            $source = [$source];
        }

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
