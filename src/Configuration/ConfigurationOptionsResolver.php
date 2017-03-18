<?php declare(strict_types=1);

namespace ApiGen\Configuration;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\Configuration\Theme\ThemeConfigFactory;
use ApiGen\Utils\FileSystem;
use ReflectionProperty;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigurationOptionsResolver
{

    const AL_PROTECTED = 'protected';
    const AL_PRIVATE = 'private';
    const AL_PUBLIC = 'public';

    /**
     * @var array
     */
    private $defaults = [
        CO::ANNOTATION_GROUPS => [],
        CO::ACCESS_LEVELS => ['public'],
        CO::BASE_URL => '',
        CO::CONFIG => '',
        CO::DEBUG => false,
        CO::DESTINATION => null,
        CO::FORCE_OVERWRITE => false,
        CO::EXCLUDE => [],
        CO::EXTENSIONS => ['php'],
        CO::GOOGLE_CSE_ID => '',
        CO::GOOGLE_ANALYTICS => '',
        CO::MAIN => '',
        CO::INTERNAL => false,
        CO::SOURCE => [],
        CO::NO_SOURCE_CODE => false,
        CO::TEMPLATE => null,
        CO::TEMPLATE_CONFIG => null,
        CO::TITLE => '',
        // helpers
        CO::VISIBILITY_LEVELS => [],
        CO::SOURCE_CODE => '',
        // removed, but BC for templates
        'download' => false,
        'tree' => false,
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
            CO::VISIBILITY_LEVELS => function (Options $options) {
                return $this->getAccessLevelForReflections($options[CO::ACCESS_LEVELS]);
            },
            CO::TEMPLATE => function (Options $options) {
                $config = $options[CO::TEMPLATE_CONFIG];
                return $this->themeConfigFactory->create($config)
                    ->getOptions();
            }
        ]);
    }


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
        $this->resolver->setRequired([CO::SOURCE, CO::DESTINATION]);
    }


    private function setAllowedValues(): void
    {
        $this->resolver->addAllowedValues(CO::DESTINATION, function ($destination) {
            return $this->allowedValuesForDestination($destination);
        });

        $this->resolver->addAllowedValues(CO::SOURCE, function ($source) {
            return $this->allowedValuesForSource($source);
        });

        $this->resolver->addAllowedValues(CO::TEMPLATE_CONFIG, function ($value) {
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
        $this->resolver->setNormalizer(CO::ANNOTATION_GROUPS, function (Options $options, $value) {
            $value = (array) $value;
            return array_unique($value);
        });

        $this->resolver->setNormalizer(CO::DESTINATION, function (Options $options, $value) {
            return $this->fileSystem->getAbsolutePath($value);
        });

        $this->resolver->setNormalizer(CO::BASE_URL, function (Options $options, $value) {
            return rtrim((string) $value, '/');
        });

        $this->resolver->setNormalizer(CO::SOURCE, function (Options $options, $value) {
            if (! is_array($value)) {
                $value = [$value];
            }
            foreach ($value as $key => $source) {
                $value[$key] = $this->fileSystem->getAbsolutePath($source);
            }
            return $value;
        });

        $this->resolver->setNormalizer(CO::SOURCE_CODE, function (Options $options) {
            return ! $options[CO::NO_SOURCE_CODE];
        });

        $this->resolver->setNormalizer(CO::TEMPLATE_CONFIG, function (Options $options, $value) {
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

    private function ensureSourceExists(string $singleSource)
    {
        if (! file_exists($singleSource)) {
            throw new ConfigurationException(sprintf(
                'Source "%s" does not exist',
                $singleSource
            ));
        }
    }
}