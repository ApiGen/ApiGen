<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\Charset\CharsetConvertor;
use ApiGen\Configuration\Theme\ThemeConfigFactory;
use ApiGen\FileSystem\FileSystem;
use Nette;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ConfigurationOptionsResolver extends Nette\Object
{

	const AL_PROTECTED = 'protected';
	const AL_PRIVATE = 'private';
	const AL_PUBLIC = 'public';
	const TEMPLATE_THEME_DEFAULT = 'default';
	const TEMPLATE_THEME_BOOTSTRAP = 'bootstrap';

	/**
	 * @var array
	 */
	private $defaults = array(
		'autocomplete' => array('classes', 'constants', 'functions'),
		'accessLevels' => array(self::AL_PUBLIC, self::AL_PROTECTED),
		'baseUrl' => '',
		'debug' => FALSE,
		'deprecated' => FALSE,
		'destination' => NULL,
		'download' => FALSE,
		'exclude' => array(),
		'extensions' => array('php'),
		'googleCseId' => '',
		'googleAnalytics' => '',
		'groups' => Configuration::GROUPS_AUTO,
		'charset' => array(),
		'main' => '',
		'internal' => FALSE,
		'php' => TRUE,
		'skipDocPath' => array(),
		'skipDocPrefix' => array(),
		'source' => array(),
		'template' => NULL,
		'templateConfig' => NULL,
		'templateTheme' => self::TEMPLATE_THEME_DEFAULT,
		'title' => '',
		'todo' => FALSE,
		'tree' => TRUE,
		// helpers
		'methodsAccessLevels' => array(),
		'propertyAccessLevels' => array()
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
		$this->resolver->setDefaults(array(
			'templateConfig' => function (Options $options, $previousValue) {
				if ($previousValue === NULL) {
					if ($options['templateTheme'] === self::TEMPLATE_THEME_DEFAULT) {
						return APIGEN_ROOT_PATH . '/templates/default/config.neon';

					} elseif ($options['templateTheme'] === self::TEMPLATE_THEME_BOOTSTRAP) {
						return APIGEN_ROOT_PATH . '/templates/bootstrap/config.neon';
					}
				}
				return $previousValue;
			},
			'template' => function (Options $options) {
				$templateConfig = $this->themeConfigFactory->create($options['templateConfig']);
				return $templateConfig->getOptions();
			}
		));
	}


	private function replaceDefaults()
	{
		$this->resolver->replaceDefaults(array(
			'methodAccessLevels' => function (Options $options) {
				return $this->getAccessLevelForReflections($options['accessLevels'], 'method');
			},
			'propertyAccessLevels' => function (Options $options) {
				return $this->getAccessLevelForReflections($options['accessLevels'], 'property');
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
		$this->resolver->setRequired(array('source', 'destination'));
	}


	private function setAllowedTypes()
	{
		$this->resolver->setAllowedTypes(array(
			'autocomplete' => 'array',
			'accessLevels' => 'array',
			'baseUrl' => 'string',
			'debug' => 'bool',
			'deprecated' => 'bool',
			'destination' => 'string',
			'download' => 'bool',
			'exclude' => 'array',
			'extensions' => 'array',
			'googleCseId' => 'string',
			'googleAnalytics' => 'string',
			'groups' => 'string',
			'charset' => 'array',
			'main' => 'string',
			'internal' => 'bool',
			'php' => 'bool',
			'skipDocPath' => 'array',
			'skipDocPrefix' => 'array',
			'source' => 'array',
			'templateConfig' => 'string',
			'title' => 'string',
			'todo' => 'bool',
			'tree' => 'bool'
		));
	}


	private function setAllowedValues()
	{
		$this->resolver->setAllowedValues(array(
			'destination' => function ($value) {
				if ( ! is_dir($value)) {
					mkdir($value, 0755, TRUE);
				}
				if ( ! is_dir($value) || ! is_writable($value)) {
					throw new ConfigurationException("Destination '$value' is not writable");
				}
				return TRUE;
			},
			'source' => function ($value) {
				foreach ($value as $source) {
					if ( ! file_exists($source)) {
						throw new ConfigurationException("Source '$source' does not exist");
					}
				}
				return TRUE;
			},
			'templateConfig' => function ($value) {
				if ( ! is_file($value)) {
					throw new ConfigurationException("Template config '$value' was not found");
				}
				return TRUE;
			}
		));
	}


	private function setNormalizers()
	{
		$this->resolver->setNormalizers(array(
			'autocomplete' => function (Options $options, $value) {
				return array_flip($value);
			},
			'destination' => function (Options $options, $value) {
				return FileSystem::getAbsolutePath($value);
			},
			'charset' => function (Options $options, $value) {
				if ($value === array('auto')) {
					return array();
				}
				return $value;
			},
			'baseUrl' => function (Options $options, $value) {
				return rtrim($value, '/');
			},
			'skipDocPath' => function (Options $options, $value) {
				foreach ($value as $key => $source) {
					$value[$key] = FileSystem::getAbsolutePath($source);
				}
				return $value;
			},
			'skipDocPrefix' => function (Options $options, $value) {
				$value = array_map(function ($prefix) {
					return ltrim($prefix, '\\');
				}, $value);
				usort($value, 'strcasecmp');
				return $value;
			},
			'source' => function (Options $options, $value) {
				foreach ($value as $key => $source) {
					$value[$key] = FileSystem::getAbsolutePath($source);
				}
				return $value;
			},
			'templateConfig' => function (Options $options, $value) {
				return FileSystem::getAbsolutePath($value);
			},
		));
	}

}
