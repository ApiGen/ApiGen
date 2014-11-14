<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\Configuration\Theme\ThemeConfigFactory;
use ApiGen\FileSystem\FileSystem;
use ApiGen\Configuration\ConfigurationOptions as CO;
use Nette;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ConfigurationOptionsResolver extends Nette\Object
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
	private $defaults = array(
		CO::AUTOCOMPLETE => array(),
		CO::ACCESS_LEVELS => array(),
		CO::BASE_URL => '',
		CO::CONFIG => '',
		CO::DEBUG => FALSE,
		CO::DEPRECATED => FALSE,
		CO::DESTINATION => NULL,
		CO::DOWNLOAD => FALSE,
		CO::EXCLUDE => array(),
		CO::EXTENSIONS => array(),
		CO::GOOGLE_CSE_ID => '',
		CO::GOOGLE_ANALYTICS => '',
		CO::GROUPS => '',
		CO::CHARSET => array(),
		CO::MAIN => '',
		CO::INTERNAL => FALSE,
		CO::PHP => TRUE,
		CO::SKIP_DOC_PATH => array(),
		CO::SKIP_DOC_PREFIX => array(),
		CO::SOURCE => array(),
		CO::SOURCE_CODE => TRUE,
		CO::TEMPLATE => NULL,
		CO::TEMPLATE_CONFIG => NULL,
		CO::TEMPLATE_THEME => '',
		CO::TITLE => '',
		CO::TODO => FALSE,
		CO::TREE => TRUE,
		// helpers
		CO::METHODS_ACCESS_LEVELS => array(),
		CO::PROPERTY_ACCESS_LEVELS => array()
	);

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
		$this->replaceDefaults();
		$this->setRequired();
		$this->setAllowedTypes();
		$this->setAllowedValues();
		$this->setNormalizers();
		return $this->resolver->resolve($options);
	}


	private function setDefaults()
	{
		$this->resolver->setDefaults($this->defaults);
	}


	private function replaceDefaults()
	{
		$this->resolver->replaceDefaults(array(
			CO::METHODS_ACCESS_LEVELS => function (Options $options) {
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
		));
	}


	/**
	 * @param array$options
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
		$this->resolver->setRequired(array(CO::SOURCE, CO::DESTINATION));
	}


	private function setAllowedTypes()
	{
		$this->resolver->setAllowedTypes(array(
			CO::AUTOCOMPLETE => 'array',
			CO::ACCESS_LEVELS => 'array',
			CO::BASE_URL => 'string',
			CO::CONFIG => 'string',
			CO::DEBUG => 'bool',
			CO::DEPRECATED => 'bool',
			CO::DESTINATION => 'string',
			CO::DOWNLOAD => 'bool',
			CO::EXCLUDE => 'array',
			CO::EXTENSIONS => 'array',
			CO::GOOGLE_CSE_ID => array('null', 'string'),
			CO::GOOGLE_ANALYTICS => array('null', 'string'),
			CO::GROUPS => 'string',
			CO::CHARSET => 'array',
			CO::MAIN => array('null', 'string'),
			CO::INTERNAL => 'bool',
			CO::PHP => 'bool',
			CO::SKIP_DOC_PATH => 'array',
			CO::SKIP_DOC_PREFIX => 'array',
			CO::SOURCE => 'array',
			CO::SOURCE_CODE => 'bool',
			CO::TEMPLATE_CONFIG => 'string',
			CO::TITLE => array('null', 'string'),
			CO::TODO => 'bool',
			CO::TREE => 'bool'
		));
	}


	private function setAllowedValues()
	{
		$this->resolver->setAllowedValues(array(
			CO::DESTINATION => function ($value) {
				if ( ! is_dir($value)) {
					mkdir($value, 0755, TRUE);
				}
				if ( ! is_dir($value) || ! is_writable($value)) {
					throw new ConfigurationException("Destination '$value' is not writable");
				}
				return TRUE;
			},
			CO::SOURCE => function ($value) {
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
		));
	}


	private function setNormalizers()
	{
		$this->resolver->setNormalizers(array(
			CO::AUTOCOMPLETE => function (Options $options, $value) {
				return array_flip($value);
			},
			CO::DESTINATION => function (Options $options, $value) {
				return FileSystem::getAbsolutePath($value);
			},
			CO::CHARSET => function (Options $options, $value) {
				if ($value === array('auto')) {
					return array();
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
			CO::SKIP_DOC_PREFIX => function (Options $options, $value) {
				$value = array_map(function ($prefix) {
					return ltrim($prefix, '\\');
				}, $value);
				usort($value, 'strcasecmp');
				return $value;
			},
			CO::SOURCE => function (Options $options, $value) {
				foreach ($value as $key => $source) {
					$value[$key] = FileSystem::getAbsolutePath($source);
				}
				return $value;
			},
			CO::TEMPLATE_CONFIG => function (Options $options, $value) {
				return FileSystem::getAbsolutePath($value);
			}
		));
	}


	/**
	 * @param string $theme
	 * @return string
	 */
	private function getTemplateConfigPathFromTheme($theme)
	{
		if ($theme === self::TEMPLATE_THEME_DEFAULT) {
			return APIGEN_ROOT_PATH . '/templates/default/config.neon';

		} elseif ($theme === self::TEMPLATE_THEME_BOOTSTRAP) {
			return APIGEN_ROOT_PATH . '/templates/bootstrap/config.neon';
		}

		throw new ConfigurationException(CO::TEMPLATE_THEME . ' ' . $theme . ' is not supported.');
	}

}
