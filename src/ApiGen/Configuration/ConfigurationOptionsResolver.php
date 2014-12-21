<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Configuration\Exceptions\ConfigurationException;
use ApiGen\Configuration\Theme\ThemeConfigFactory;
use ApiGen\FileSystem\FileSystem;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ConfigurationOptionsResolver
{

	const AC_CLASSES = 'classes';
	const AC_CONSTANTS = 'constants';
	const AC_FUNCTIONS = 'functions';
	const AL_PROTECTED = 'protected';
	const AL_PRIVATE = 'private';
	const AL_PUBLIC = 'public';
	const TEMPLATE_THEME_DEFAULT = 'default';
	const TEMPLATE_THEME_BOOTSTRAP = 'bootstrap';

	/**
	 * @var array
	 */
	private $defaults = [
		CO::AUTOCOMPLETE => [],
		CO::ACCESS_LEVELS => [],
		CO::BASE_URL => '',
		CO::CONFIG => '',
		CO::DEBUG => FALSE,
		CO::DEPRECATED => FALSE,
		CO::DESTINATION => NULL,
		CO::DOWNLOAD => FALSE,
		CO::EXCLUDE => [],
		CO::EXTENSIONS => [],
		CO::GOOGLE_CSE_ID => '',
		CO::GOOGLE_ANALYTICS => '',
		CO::GROUPS => '',
		CO::CHARSET => [],
		CO::MAIN => '',
		CO::INTERNAL => FALSE,
		CO::PHP => FALSE,
		CO::SKIP_DOC_PATH => [],
		CO::SOURCE => [],
		CO::NO_SOURCE_CODE => FALSE,
		CO::TEMPLATE => NULL,
		CO::TEMPLATE_CONFIG => NULL,
		CO::TEMPLATE_THEME => self::TEMPLATE_THEME_DEFAULT,
		CO::TITLE => '',
		CO::TODO => FALSE,
		CO::TREE => TRUE,
		// helpers
		CO::METHOD_ACCESS_LEVELS => [],
		CO::PROPERTY_ACCESS_LEVELS => [],
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


	public function __construct(ThemeConfigFactory $themeConfigFactory, OptionsResolverFactory $optionsResolverFactory)
	{
		$this->themeConfigFactory = $themeConfigFactory;
		$this->optionsResolverFactory = $optionsResolverFactory;
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
			CO::METHOD_ACCESS_LEVELS => function (Options $options) {
				return $this->getAccessLevelForReflections($options[CO::ACCESS_LEVELS], 'method');
			},
			CO::PROPERTY_ACCESS_LEVELS => function (Options $options) {
				return $this->getAccessLevelForReflections($options[CO::ACCESS_LEVELS], 'property');
			},
			CO::TEMPLATE => function (Options $options) {
				if ( ! $options[CO::TEMPLATE_CONFIG]) {
					$config = $this->getTemplateConfigPathFromTheme($options[CO::TEMPLATE_THEME]);

				} else {
					$config = $options[CO::TEMPLATE_CONFIG];
				}
				return $this->themeConfigFactory->create($config)->getOptions();
			}
		]);
	}


	/**
	 * @param array $options
	 * @param string $type
	 * @return int
	 */
	private function getAccessLevelForReflections($options, $type)
	{
		$accessLevel = NULL;
		if (in_array(self::AL_PUBLIC, $options)) {
			$accessLevel |= ($type === 'property' ? ReflectionProperty::IS_PUBLIC : ReflectionMethod::IS_PUBLIC);
		}

		if (in_array(self::AL_PROTECTED, $options)) {
			$accessLevel |= ($type === 'property' ? ReflectionProperty::IS_PROTECTED : ReflectionMethod::IS_PROTECTED);
		}

		if (in_array(self::AL_PRIVATE, $options)) {
			$accessLevel |= ($type === 'property' ? ReflectionProperty::IS_PRIVATE : ReflectionMethod::IS_PRIVATE);
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
		$this->resolver->setAllowedValues([
			CO::DESTINATION => function ($value) {
				if ( ! $value) {
					throw new ConfigurationException("Destination is not set. Use '-d <dir>' or config to set it");
				}
				if ( ! is_dir($value)) {
					mkdir($value, 0755, TRUE);
				}
				if ( ! is_dir($value) || ! is_writable($value)) {
					throw new ConfigurationException("Destination '$value' is not writable");
				}
				return TRUE;
			},
			CO::SOURCE => function ($value) {
				if ( ! $value) {
					throw new ConfigurationException("Source is not set. Use '-s <dir>' or config to set it");
				}
				if ( ! is_array($value)) {
					$value = [$value];
				}
				foreach ($value as $source) {
					if ( ! file_exists($source)) {
						throw new ConfigurationException("Source '$source' does not exist");
					}
				}
				return TRUE;
			},
			CO::TEMPLATE_CONFIG => function ($value) {
				if ($value && ! is_file($value)) {
					throw new ConfigurationException("Template config '$value' was not found");
				}
				return TRUE;
			}
		]);
	}


	private function setNormalizers()
	{
		$this->resolver->setNormalizers([
			CO::AUTOCOMPLETE => function (Options $options, $value) {
				return array_flip($value);
			},
			CO::DESTINATION => function (Options $options, $value) {
				return FileSystem::getAbsolutePath($value);
			},
			CO::CHARSET => function (Options $options, $value) {
				if ($value === ['auto']) {
					return [];
				}
				return $value;
			},
			CO::BASE_URL => function (Options $options, $value) {
				return rtrim($value, '/');
			},
			CO::SKIP_DOC_PATH => function (Options $options, $value) {
				foreach ($value as $key => $source) {
					$value[$key] = FileSystem::getAbsolutePath($source);
				}
				return $value;
			},
			CO::SOURCE => function (Options $options, $value) {
				if ( ! is_array($value)) {
					$value = [$value];
				}
				foreach ($value as $key => $source) {
					$value[$key] = FileSystem::getAbsolutePath($source);
				}
				return $value;
			},
			CO::SOURCE_CODE => function (Options $options) {
				return ! $options[CO::NO_SOURCE_CODE];
			},
			CO::TEMPLATE_CONFIG => function (Options $options, $value) {
				return FileSystem::getAbsolutePath($value);
			}
		]);
	}


	/**
	 * @param string $theme
	 * @return string
	 */
	private function getTemplateConfigPathFromTheme($theme)
	{
		$rootPath = defined('APIGEN_ROOT_PATH') ? APIGEN_ROOT_PATH : getcwd() . '/src';
		if ($theme === self::TEMPLATE_THEME_DEFAULT) {
			return $rootPath . '/templates/default/config.neon';

		} elseif ($theme === self::TEMPLATE_THEME_BOOTSTRAP) {
			return $rootPath . '/templates/bootstrap/config.neon';
		}

		throw new ConfigurationException(CO::TEMPLATE_THEME . ' ' . $theme . ' is not supported.');
	}

}
