<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use ApiGen\Configuration\Validator;
use Nette;
use Nette\Neon\Neon;


class Config
{
	/**
	 * @var array
	 */
	private $config = array();

	/**
	 * @var array
	 */
	protected $defaults = array(
		'source' => array(),
		'destination' => '',
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
		'templateConfig' => '',
		'allowedHtml' => array('b', 'i', 'a', 'ul', 'ol', 'li', 'p', 'br', 'var', 'samp', 'kbd', 'tt'),
		'groups' => 'auto',
		'autocomplete' => array('classes', 'constants', 'functions'),
		'accessLevels' => array('public', 'protected'),
		'internal' => false,
		'php' => true,
		'tree' => true,
		'deprecated' => false,
		'todo' => false,
		'download' => false,
		'report' => '',
		'wipeout' => true,
		'colors' => true,
		'debug' => false,
		'template' => array(
			'require' => array(),
			'resources' => array(),
			'templates' => array(
				'common' => array(),
				'optional' => array()
			)
		)
	);

	/**
	 * File or directory path options.
	 *
	 * @var array
	 */
	private static $pathOptions = array(
		'source',
		'destination',
		'templateConfig',
		'report'
	);

	/** @var Validator */
	private $configValidator;


	/**
	 * @param array $options
	 * @return \ApiGen\Config
	 */
	public function __construct($options)
	{
		$this->configValidator = new Validator;

		$templateDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
		$this->defaults['templateConfig'] = $templateDir . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'config.neon';
		$this->defaults['colors'] = 'WIN' === substr(PHP_OS, 0, 3) ? false : (function_exists('posix_isatty') && defined('STDOUT') ? posix_isatty(STDOUT) : true);

		$config = $this->processCliOptions($options);
		$this->config = $this->prepare($config);


		return $this;
	}

	/**
	 * Processes command line options.
	 *
	 * @param array $options
	 * @return \ApiGen\Config
	 * @todo turn to Symfony\Console command
	 */
	public function processCliOptions(array $options)
	{
		$tmp = array();
		while ($option = current($options)) {
			if (preg_match('~^--([a-z][-a-z]*[a-z])(?:=(.+))?$~', $option, $matches) || preg_match('~^-([a-z])=?(.*)~', $option, $matches)) {
				$name = $matches[1];

				if (!empty($matches[2])) {
					$value = $matches[2];
				} else {
					$next = next($options);
					if (false === $next || '-' === $next{0}) {
						prev($options);
						$value = '';
					} else {
						$value = $next;
					}
				}

				$tmp[$name][] = $value;
			}

			next($options);
		}

		$options = $tmp;

		$options = array_map(function($value) {
			return 1 === count($value) ? $value[0] : $value;
		}, $options);

		return $options;
	}


	/**
	 * @param array
	 * @return \ApiGen\Config
	 * @throws \ApiGen\ConfigException If something in configuration is wrong.
	 */
	public function prepare($config)
	{
		if ( ! isset($config['config'])) {
			throw new ConfigException('Parameter "--config" is required');
		}

		$configFile = $config['config'];

		if ( ! is_file($configFile)) {
			throw new ConfigException('File ' . $configFile . 'was not found');
		}

		$neon = Neon::decode(file_get_contents($configFile));
		$config = array_merge($config, $neon);
		$config = $this->absolutizePaths($config);
		$config = array_merge($this->defaults, $config);
		if ( ! is_array($config['source'])) {
			$config['source'] = array($config['source']);
		}

		$this->configValidator->validateConfig($config);

		// Unify character sets
		$config['charset'] = array_map('strtoupper', $config['charset']);

		// Process options that specify a filesystem path
		$config = $this->processPathOptions($config);

		$config = $this->unifyDirectorySeparators($config);

		$config = $this->unifyPrefixes($config);

		// Base url without slash at the end
		$config['baseUrl'] = rtrim($config['baseUrl'], '/');

		// Merge template config
		$config = array_merge_recursive($config, array(
			'template' => Neon::decode(file_get_contents($fileName = $config['templateConfig'])))
		);

		$config['template']['config'] = realpath($fileName);

		// Check template
		$this->configValidator->validateTemplateConfig($config);

		return $config;
	}


	/**
	 * @param array $config
	 * @return array
	 */
	protected function unifyDirectorySeparators($config)
	{
		foreach (array('exclude', 'skipDocPath') as $option) {
			$config[$option] = array_map(function ($mask) {
				return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $mask);
			}, $config[$option]);
			usort($config[$option], 'strcasecmp');
		}

		return $config;
	}


	/**
	 * @param array $config
	 * @return array
	 */
	protected function unifyPrefixes($config)
	{
		$config['skipDocPrefix'] = array_map(function ($prefix) {
			return ltrim($prefix, '\\');
		}, $config['skipDocPrefix']);
		usort($config['skipDocPrefix'], 'strcasecmp');

		return $config;
	}


	/**
	 * @param array $neon
	 * @return mixed
	 */
	protected function absolutizePaths($neon)
	{
		foreach (self::$pathOptions as $option) {
			if (!empty($neon[$option])) {
				if (is_array($neon[$option])) {
					foreach ($neon[$option] as $key => $value) {
						$neon[$option][$key] = $this->getAbsolutePath($value, $neon);
					}

				} else {
					$neon[$option] = $this->getAbsolutePath($neon[$option], $neon);
				}
			}
		}
		return $neon;
	}


	/**
	 * Returns absolute path.
	 *
	 * @param string $path Path
	 * @return string
	 */
	private function getAbsolutePath($path, $config)
	{
		if (preg_match('~/|[a-z]:~Ai', $path)) {
			return $path;
		}

		return dirname($config['config']) . DIRECTORY_SEPARATOR . $path;
	}


	/************************ array access ************************/


//	/**
//	 * Checks if a configuration option exists.
//	 *
//	 * @param string $name Option name
//	 * @return boolean
//	 */
//	public function __isset($name)
//	{
//		return isset($this->config[$name]);
//	}
//

	/**
	 * Returns a configuration option value.
	 *
	 * @param string $name Option name
	 * @return mixed
	 */
	public function __get($name)
	{
		return isset($this->config[$name]) ? $this->config[$name] : NULL;
	}


	/**
	 * @param array $config
	 * @return array
	 *
	 */
	protected function processPathOptions($config)
	{
		foreach (self::$pathOptions as $option) {
			if (is_array($config[$option])) {
				array_walk($config[$option], function (&$value) {
					if (file_exists($value)) {
						$value = realpath($value);
					}
				});
				usort($config[$option], 'strcasecmp');

			} else {
				if (file_exists($config[$option])) {
					$config[$option] = realpath($config[$option]);
				}
			}
		}

		return $config;
	}

}
