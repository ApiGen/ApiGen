<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration;

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
	public $onSuccessValidate = array();

	/**
	 * @var array
	 */
	private $defaults = array(
		'extensions' => array('php'),
		'exclude' => array(),
		'skipDocPath' => array(),
		'skipDocPrefix' => array(),
		'charset' => array('auto'),
		'main' => '',
		'title' => '',
		'baseUrl' => '',
		'googleCseId' => '',
		'googleAnalytics' => '',
		'groups' => 'auto',
		'autocomplete' => array('classes', 'constants', 'functions'),
		'accessLevels' => array('public', 'protected'),
		'internal' => FALSE,
		'php' => TRUE,
		'tree' => TRUE,
		'deprecated' => FALSE,
		'todo' => FALSE,
		'download' => FALSE,
		// templates
		'templateTheme' => self::TEMPLATE_THEME_DEFAULT
	);

	/**
	 * File or directory path options.
	 *
	 * @var array
	 */
	private $pathOptions = array(
		'source',
		'destination',
		'templateConfig',
		'exclude',
		'skipDocPath'
	);


	/**
	 * @param array $config
	 * @return array
	 */
	public function setDefaults(array $config)
	{
		foreach ($this->defaults as $key => $value) {
			if ( ! isset($config[$key])) {
				$config[$key] = $value;
			}
		}

		// Set template theme path
		$isThemeUsed = FALSE;
		if ( ! isset($config['templateConfig'])) {
			if ($config['templateTheme'] === self::TEMPLATE_THEME_DEFAULT) {
				$config['templateConfig'] = APIGEN_ROOT_PATH . '/templates/default/config.neon';

			} elseif ($config['templateTheme'] === self::TEMPLATE_THEME_BOOTSTRAP) {
				$config['templateConfig'] = APIGEN_ROOT_PATH . '/templates/bootstrap/config.neon';
			}
			$isThemeUsed = TRUE;
		}

		// Merge template configuration
		$templateConfigFile = new NeonFile($config['templateConfig']);
		$config['template'] = $templateConfigFile->read();

		// Fix paths for themes
		if ($isThemeUsed) {
			$config = $this->correctThemeTemplatePaths($config);
		}

		$this->validate($config);
		$this->sanitaze($config);

		return $config;
	}


	/**
	 * @param array $config
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

		$this->validateTemplateConfig($config);

		$this->onSuccessValidate($config);
	}


	/**
	 * @param array $config
	 */
	private function validateTemplateConfig($config)
	{
		if ( ! is_file($config['templateConfig'])) {
			throw new ConfigurationException($config['templateConfig'] . ' was not found. Fix templateConfig option');
		}

		foreach (array('main', 'optional') as $section) {
			foreach ($config['template']['templates'][$section] as $type => $configSection) {
				if ( ! isset($configSection['filename'])) {
					throw new ConfigurationException("Filename for $type is not defined");
				}

				if ( ! isset($configSection['template'])) {
					throw new ConfigurationException("Template for $type is not defined");
				}

				if ( ! is_file(dirname($config['templateConfig']) . DS . $configSection['template'])) {
					throw new ConfigurationException("Template for $type does not exist");
				}
			}
		}
	}


	/**
	 * @param array $config
	 * @return array
	 */
	private function sanitaze(array $config)
	{
		// Unify character sets
		$config['charset'] = array_map('strtoupper', $config['charset']);

		// Process options that specify a filesystem path
		foreach ($this->pathOptions as $option) {
			if (is_array($config[$option])) {
				array_walk($config[$option], function (&$value) {
					if (file_exists($value)) {
						$value = realpath($value);

					} else {
						$value = str_replace(array('/', '\\'), DS, $value);
					}
				});
				usort($config[$option], 'strcasecmp');

			} else {
				if (file_exists($config[$option])) {
					$config[$option] = realpath($config[$option]);

				} else {
					$config[$option] = str_replace(array('/', '\\'), DS, $config[$option]);
				}
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


	/**
	 * @param array $config
	 * @return array
	 */
	private function correctThemeTemplatePaths(array $config)
	{
		$templateDir = dirname($config['templateConfig']);

		foreach (array('main', 'optional') as $section) {
			foreach ($config['template']['templates'][$section] as $type => $configSection) {
				$configSection['template'] = $templateDir . DS . $configSection['template'];
			}
		}

		return $config;
	}

}


/**
 * Thrown when an invalid configuration is detected.
 */
class ConfigurationException extends \RuntimeException
{

}
