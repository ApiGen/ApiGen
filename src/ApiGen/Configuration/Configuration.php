<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

use ApiGen\Configuration\Theme\ThemeConfigFactory;
use ApiGen\Factory;
use ApiGen\FileSystem\FileSystem;
use ApiGen\Neon\NeonFile;
use Nette;
use Nette\Utils\AssertionException;
use Nette\Utils\Validators;


/**
 * @method Configuration onSuccessValidate(array $config)
 */
class Configuration extends Nette\Object
{

	const TEMPLATE_THEME_DEFAULT = 'default';
	const TEMPLATE_THEME_BOOTSTRAP = 'bootstrap';

	/**
	 * Static access for reflections
	 *
	 * @var Nette\Utils\ArrayHash
	 */
	public static $config;

	/**
	 * @var array
	 */
	public $onSuccessValidate = [];

	/**
	 * @var mixed[]
	 */
	private $defaults = [
		'extensions' => ['php'],
		'exclude' => [],
		'skipDocPath' => [],
		'skipDocPrefix' => [],
		'charset' => ['auto'],
		'main' => '',
		'title' => '',
		'baseUrl' => '',
		'googleCseId' => '',
		'googleAnalytics' => '',
		'groups' => 'auto',
		'autocomplete' => ['classes', 'constants', 'functions'],
		'accessLevels' => ['public', 'protected'],
		'internal' => FALSE,
		'php' => TRUE,
		'tree' => TRUE,
		'deprecated' => FALSE,
		'todo' => FALSE,
		'download' => FALSE,
		// templates
		'templateTheme' => self::TEMPLATE_THEME_DEFAULT,
		'template' => NULL
	];

	/**
	 * File or directory path options.
	 *
	 * @var string[]
	 */
	private $pathOptions = [
		'source',
		'destination',
		'exclude',
		'skipDocPath'
	];

	/**
	 * @var ThemeConfigFactory
	 */
	private $themeConfigFactory;


	public function __construct(ThemeConfigFactory $themeConfigFactory)
	{
		$this->themeConfigFactory = $themeConfigFactory;
	}


	/**
	 * @return array
	 */
	public function setDefaults(array $config)
	{
		foreach ($this->defaults as $key => $value) {
			if ( ! isset($config[$key])) {
				$config[$key] = $value;
			}
		}

		if ( ! isset($config['templateConfig'])) {
			$config['templateConfig'] = $this->getTemplateConfigPathFromTheme($config['templateTheme']);
		}
		$config['template'] = $this->themeConfigFactory->create($config['templateConfig'])->getOptions();

		$config = $this->sanitaze($config);
		$this->validate($config);

		return $config;
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
		throw new ConfigurationException('Template theme  ' . $theme . ' is not supported.');
	}


	/**
	 * @throws AssertionException
	 */
	private function validate(array $config)
	{
		Validators::assertField($config, 'exclude', 'list');
		Validators::assertField($config, 'skipDocPath', 'list');
		Validators::assertField($config, 'skipDocPrefix', 'list');
		Validators::assertField($config, 'charset', 'list');
		Validators::assertField($config, 'main', 'string');
		Validators::assertField($config, 'title', 'string');
		Validators::assertField($config, 'baseUrl', 'string');
		Validators::assertField($config, 'googleCseId', 'string');
		Validators::assertField($config, 'googleAnalytics', 'string');
		Validators::assertField($config, 'autocomplete', 'list');
		Validators::assertField($config, 'accessLevels', 'list');
		Validators::assertField($config, 'internal', 'bool');
		Validators::assertField($config, 'php', 'bool');
		Validators::assertField($config, 'tree', 'bool');
		Validators::assertField($config, 'deprecated', 'bool');
		Validators::assertField($config, 'todo', 'bool');
		Validators::assertField($config, 'download', 'bool');

		// destination
		Validators::assertField($config, 'destination', 'string');
		@mkdir($config['destination'], 0755, TRUE);
		if ( ! is_dir($config['destination']) || ! is_writable($config['destination'])) {
			throw new \RuntimeException('Directory ' . $config['destination'] . ' is not writable.');
		}

		// source
		Validators::assertField($config, 'source', 'array');
		foreach ($config['source'] as $source) {
			if ( ! file_exists($source)) {
				throw new ConfigurationException("Source $source does not exist");
			}
		}

		// extensions
		Validators::assertField($config, 'extensions', 'array');
		foreach ($config['extensions'] as $extension) {
			Validators::assert($extension, 'string', 'file extension');
		}

		$this->onSuccessValidate($config);
	}


	/**
	 * @param string $path
	 * @return string
	 */
	private function sanitazePathHelper(&$path)
	{
		$path = FileSystem::normalizePath($path);

		if ((strpos($path, 'phar://') !== 0) && file_exists($path)) {
			$path = realpath($path);
		}

		return $path;
	}


	/**
	 * @return array
	 */
	private function sanitaze(array $config)
	{
		// Unify character sets
		$config['charset'] = array_map('strtoupper', $config['charset']);

		// Process options that specify a filesystem path
		foreach ($this->pathOptions as $option) {
			if (is_array($config[$option])) {
				array_walk($config[$option], [$this, 'sanitazePathHelper']);
				usort($config[$option], 'strcasecmp');

			} else {
				$config[$option] = $this->sanitazePathHelper($config[$option]);
			}
		}

		// Unify prefixes
		$config['skipDocPrefix'] = array_map(function ($prefix) {
			return ltrim($prefix, '\\');
		}, $config['skipDocPrefix']);
		usort($config['skipDocPrefix'], 'strcasecmp');

		// Base url without slash at the end
		$config['baseUrl'] = rtrim($config['baseUrl'], '/');

		return $config;
	}

}


/**
 * Thrown when an invalid configuration is detected.
 */
class ConfigurationException extends \RuntimeException
{

}
