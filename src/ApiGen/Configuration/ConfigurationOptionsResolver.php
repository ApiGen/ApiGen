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
			CO::VISIBILITY_LEVELS => function (Options $options) {
				return $this->getAccessLevelForReflections($options[CO::ACCESS_LEVELS]);
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
	 * @return int
	 */
	private function getAccessLevelForReflections(array $options)
	{
		$accessLevel = NULL;
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
			return TRUE;
		});
	}


	private function setNormalizers()
	{
		$this->resolver->setNormalizers([
			CO::DESTINATION => function (Options $options, $value) {
				return FileSystem::getAbsolutePath($value);
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


	/**
	 * @param string $destination
	 * @return bool
	 */
	private function allowedValuesForDestination($destination)
	{
		if ( ! $destination) {
			throw new ConfigurationException("Destination is not set. Use '-d <dir>' or config to set it");

		} elseif ( ! is_dir($destination)) {
			mkdir($destination, 0755, TRUE);
		}

		if ( ! is_writable($destination)) {
			throw new ConfigurationException("Destination '$destination' is not writable");
		}
		return TRUE;
	}


	/**
	 * @param string|array $source
	 * @return bool
	 */
	private function allowedValuesForSource($source)
	{
		if ( ! $source) {
			throw new ConfigurationException("Source is not set. Use '-s <dir>' or config to set it");

		} elseif ( ! is_array($source)) {
			$source = [$source];
		}

		foreach ($source as $dir) {
			if ( ! file_exists($dir)) {
				throw new ConfigurationException("Source '$dir' does not exist");
			}
		}
		return TRUE;
	}

}
