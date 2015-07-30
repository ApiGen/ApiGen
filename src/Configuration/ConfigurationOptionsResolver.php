<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\Configuration\Theme\ThemeConfigFactory;
use ApiGen\Theme\ThemeConfigPathResolver;
use ApiGen\Utils\FileSystem;
use ReflectionProperty;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationOptionsResolver
{

    const AL_PROTECTED = 'protected';
    const AL_PRIVATE = 'private';
    const AL_PUBLIC = 'public';
    const TEMPLATE_THEME_DEFAULT = 'default';
    const TEMPLATE_THEME_BOOTSTRAP = 'bootstrap';

    /**
     * @var array
     */
    private $defaults = [
        CO::ANNOTATION_GROUPS => [],
        CO::ACCESS_LEVELS => ['public'],
        CO::BASE_URL => '',
        CO::CONFIG => '',
        CO::DEBUG => false,
        CO::DEPRECATED => false,
        CO::DESTINATION => null,
        CO::DOWNLOAD => false,
        CO::EXCLUDE => [],
        CO::EXTENSIONS => [],
        CO::GOOGLE_CSE_ID => '',
        CO::GOOGLE_ANALYTICS => '',
        CO::GROUPS => '',
        CO::MAIN => '',
        CO::INTERNAL => false,
        CO::PHP => false,
        CO::SOURCE => [],
        CO::NO_SOURCE_CODE => false,
        CO::TEMPLATE => null,
        CO::TEMPLATE_CONFIG => null,
        CO::TEMPLATE_THEME => self::TEMPLATE_THEME_DEFAULT,
        CO::TITLE => '',
        CO::TODO => false,
        CO::TREE => true,
        // helpers
        CO::VISIBILITY_LEVELS => [],
        CO::SOURCE_CODE => ''
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
     * @var ThemeConfigPathResolver
     */
    private $themeConfigPathResolver;

    /**
     * @var FileSystem
     */
    private $fileSystem;


    public function __construct(
        ThemeConfigFactory $themeConfigFactory,
        OptionsResolverFactory $optionsResolverFactory,
        ThemeConfigPathResolver $themeConfigPathResolver,
        FileSystem $fileSystem
    ) {
        $this->themeConfigFactory = $themeConfigFactory;
        $this->optionsResolverFactory = $optionsResolverFactory;
        $this->themeConfigPathResolver = $themeConfigPathResolver;
        $this->fileSystem = $fileSystem;
    }


    /**
     * @return array
     */
    public function resolve(array $options)
    {
        $this->resolver = $this->optionsResolverFactory->create();
        $this->setDefaults();
        $this->setRequired();
        $this->setAllowedValues();
        $this->setNormalizers();
        return $this->resolver->resolve($options);
    }


    private function setDefaults()
    {
        $this->resolver->setDefaults($this->defaults);
        $this->resolver->setDefaults([
            CO::VISIBILITY_LEVELS => function (Options $options) {
                return $this->getAccessLevelForReflections($options[CO::ACCESS_LEVELS]);
            },
            CO::TEMPLATE => function (Options $options) {
                if (! $options[CO::TEMPLATE_CONFIG]) {
                    $config = $this->getTemplateConfigPathFromTheme($options[CO::TEMPLATE_THEME]);

                } else {
                    $config = $options[CO::TEMPLATE_CONFIG];
                }
                return $this->themeConfigFactory->create($config)->getOptions();
            }
        ]);
    }


    /**
     * @return int
     */
    private function getAccessLevelForReflections(array $options)
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


    private function setRequired()
    {
        $this->resolver->setRequired([
            CO::SOURCE,
            CO::DESTINATION
        ]);
    }


    private function setAllowedValues()
    {
        $this->resolver->addAllowedValues(CO::DESTINATION, function ($destination) {
            return $this->allowedValuesForDestination($destination);
        });

        $this->resolver->addAllowedValues(CO::SOURCE, function ($source) {
            return $this->allowedValuesForSource($source);
        });

        $this->resolver->addAllowedValues(CO::TEMPLATE_CONFIG, function ($value) {
            if ($value && ! is_file($value)) {
                throw new ConfigurationException("Template config '$value' was not found");
            }
            return true;
        });
    }


    private function setNormalizers()
    {
        $this->resolver->setNormalizer(CO::ANNOTATION_GROUPS, function (Options $options, $value) {
            $value = (array) $value;
            if ($options[CO::DEPRECATED]) {
                $value[] = CO::DEPRECATED;
            }
            if ($options[CO::TODO]) {
                $value[] = CO::TODO;
            }
            return array_unique($value);
        });

        $this->resolver->setNormalizer(CO::DESTINATION, function (Options $options, $value) {
            return $this->fileSystem->getAbsolutePath($value);
        });

        $this->resolver->setNormalizer(CO::BASE_URL, function (Options $options, $value) {
            return rtrim($value, '/');
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
            return $this->fileSystem->getAbsolutePath($value);
        });
    }


    /**
     * @param string $theme
     * @return string
     */
    private function getTemplateConfigPathFromTheme($theme)
    {
        if ($theme === self::TEMPLATE_THEME_DEFAULT) {
            return $this->themeConfigPathResolver->resolve('/vendor/apigen/theme-default/src/config.neon');

        } elseif ($theme === self::TEMPLATE_THEME_BOOTSTRAP) {
            return $this->themeConfigPathResolver->resolve('/vendor/apigen/theme-bootstrap/src/config.neon');
        }

        throw new ConfigurationException(CO::TEMPLATE_THEME . ' ' . $theme . ' is not supported.');
    }


    /**
     * @param string $destination
     * @return bool
     */
    private function allowedValuesForDestination($destination)
    {
        if (! $destination) {
            throw new ConfigurationException("Destination is not set. Use '-d <dir>' or config to set it");

        } elseif (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        if (! is_writable($destination)) {
            throw new ConfigurationException("Destination '$destination' is not writable");
        }
        return true;
    }


    /**
     * @param string|array $source
     * @return bool
     */
    private function allowedValuesForSource($source)
    {
        if (! $source) {
            throw new ConfigurationException("Source is not set. Use '-s <dir>' or config to set it");

        } elseif (! is_array($source)) {
            $source = [$source];
        }

        foreach ($source as $singleSource) {
            if (! file_exists($singleSource)) {
                throw new ConfigurationException("Source '$singleSource' does not exist");
            }
        }
        return true;
    }
}
