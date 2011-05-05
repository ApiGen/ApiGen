<?php

/**
 * ApiGen - API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

namespace Apigen;

use Apigen\Exception;
use Nette\Utils\Neon;

class Config
{
	/**
	 * Options.
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Parsed configuration.
	 *
	 * @var array
	 */
	private $config = array();

	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	private static $defaultConfig = array(
		'config' => '',
		'source' => array(),
		'destination' => '',
		'exclude' => array(),
		'skipDocPath' => array(),
		'skipDocPrefix' => array(),
		'title' => '',
		'baseUrl' => '',
		'googleCse' => '',
		'template' => 'default',
		'templateDir' => '',
		'allowedHtml' => array('b', 'i', 'a', 'ul', 'ol', 'li', 'p', 'br', 'var', 'samp', 'kbd'),
		'accessLevels' => array('public', 'protected'),
		'deprecated' => false,
		'code' => true,
		'wipeout' => true,
		'progressbar' => true,
		'debug' => false
	);

	/**
	 * File or directory path options.
	 *
	 * @var array
	 */
	private static $pathOptions = array(
		'config',
		'source',
		'destination',
		'exclude',
		'skipDocPath',
		'templateDir'
	);

	/**
	 * Possible values for options with list of values.
	 *
	 * @var array
	 */
	private static $arrayOptionsValues = array(
		'accessLevels' => array('public', 'protected', 'private')
	);

	/**
	 * Initializes configuration.
	 *
	 * @param array $options Configuration options from the command line
	 */
	public function __construct(array $options)
	{
		$this->options = $options;

		$this->config = self::$defaultConfig;
		$this->config['templateDir'] = realpath(__DIR__ . '/../../templates');
	}

	/**
	 * Parses options and configuration.
	 *
	 * @return \Apigen\Config
	 */
	public function parse()
	{
		// Compatibility
		foreach (array('config', 'source', 'destination') as $option) {
			if (isset($this->options[$option{0}]) && !isset($this->options[$option])) {
				$this->options[$option] = $this->options[$option{0}];
			}
			unset($this->options[$option{0}]);
		}

		if (!isset($this->options['config']) && !isset($this->options['source'], $this->options['destination'])) {
			throw new Exception('Missing required options', Exception::INVALID_CONFIG);
		}

		// Config file
		if (isset($this->options['config'])) {
			if (!is_file($this->options['config'])) {
				throw new Exception(sprintf('Config file %s doesn\'t exist', $this->options['config']), Exception::INVALID_CONFIG);
			}

			$this->config = array_merge($this->config, Neon::decode(file_get_contents($this->options['config'])));
		}

		// Parse options
		foreach ($this->options as $option => $value) {
			$option = preg_replace_callback('#-([a-z])#', function($matches) {
				return ucfirst($matches[1]);
			}, $option);

			$this->config[$option] = $value;
		}

		foreach (self::$defaultConfig as $option => $valueDefinition) {
			if (is_bool($valueDefinition)) {
				// Boolean option
				$value = strtolower($this->config[$option]);
				if ('on' === $value || 'yes' === $value) {
					$value = true;
				} elseif ('off' === $value || 'no' === $value) {
					$value = false;
				}
				$this->config[$option] = (bool) $value;
			} elseif (is_array($valueDefinition)) {
				// Array option
				$this->config[$option] = array_unique((array) $this->config[$option]);
				foreach ($this->config[$option] as $key => $value) {
					$value = explode(',', $value);
					while (count($value) > 1) {
						array_push($this->config[$option], array_shift($value));
					}
					$this->config[$option][$key] = array_shift($value);
				}
				$this->config[$option] = array_filter($this->config[$option]);

				if (!empty(self::$arrayOptionsValues[$option])) {
					$values = self::$arrayOptionsValues[$option];
					$this->config[$option] = array_filter($this->config[$option], function($value) use ($values) {
						return in_array($value, $values);
					});
				}
			}
		}

		// Process options that specify a filesystem path
		foreach (self::$pathOptions as $option) {
			if (is_array($this->config[$option])) {
				array_walk($this->config[$option], function(&$value) {
					if (file_exists($value)) {
						$value = realpath($value);
					}
				});
				sort($this->config[$option]);
			} else {
				if (file_exists($this->config[$option])) {
					$this->config[$option] = realpath($this->config[$option]);
				}
			}
		}

		$this->config['skipDocPrefix'] = array_map(function($prefix) {
			return ltrim($prefix, '\\');
		}, $this->config['skipDocPrefix']);
		sort($this->config['skipDocPrefix']);

		// Check
		$this->check();

		// Merge template config
		$this->config = array_merge($this->config, Neon::decode(file_get_contents($this->getTemplateConfig())));

		return $this;
	}

	/**
	 * Checks configuration.
	 *
	 * @return \Apigen\Config
	 */
	private function check()
	{
		if (empty($this->config['source'])) {
			throw new Exception('Source is not set', Exception::INVALID_CONFIG);
		}
		foreach ($this->config['source'] as $source) {
			if (!file_exists($source)) {
				throw new Exception(sprintf('Source %s doesn\'t exist', $source), Exception::INVALID_CONFIG);
			}
		}
		foreach ($this->config['source'] as $source) {
			foreach ($this->config['source'] as $source2) {
				if ($source !== $source2 && 0 === strpos($source, $source2)) {
					throw new Exception(sprintf('Sources %s and %s overlap', $source, $source2), Exception::INVALID_CONFIG);
				}
			}
		}

		if (empty($this->config['destination'])) {
			throw new Exception('Destination is not set', Exception::INVALID_CONFIG);
		}

		if (empty($this->config['templateDir'])) {
			throw new Exception('Template directory is not set', Exception::INVALID_CONFIG);
		}
		if (!is_dir($this->config['templateDir'])) {
			throw new Exception(sprintf('Template directory %s doesn\'t exist', $this->config['templateDir']), Exception::INVALID_CONFIG);
		}
		if (empty($this->config['template'])) {
			throw new Exception('Template is not set', Exception::INVALID_CONFIG);
		}
		$templateConfig = $this->getTemplateConfig();
		if (!is_dir(dirname($templateConfig))) {
			throw new Exception('Template doesn\'t exist', Exception::INVALID_CONFIG);
		}
		if (!is_file($templateConfig)) {
			throw new Exception('Template config doesn\'t exist', Exception::INVALID_CONFIG);
		}

		if (empty($this->config['accessLevels'])) {
			throw new Exception('No supported access level given', Exception::INVALID_CONFIG);
		}

		return $this;
	}

	/**
	 * Returns template config path.
	 *
	 * @return string
	 */
	private function getTemplateConfig()
	{
		return $this->config['templateDir'] . DIRECTORY_SEPARATOR . $this->config['template'] . DIRECTORY_SEPARATOR . 'config.neon';
	}

	/**
	 * Checks it a configuration option exists.
	 *
	 * @param string $name Option name
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->config[$name]);
	}

	/**
	 * Returns a configuration option value.
	 *
	 * @param string $name Option name
	 * @return mixed
	 */
	public function __get($name)
	{
		return isset($this->config[$name]) ? $this->config[$name] : null;
	}

	/**
	 * Sets a configuration option.
	 *
	 * @param string $name Option name
	 * @param mixed $value Option value
	 */
	public function __set($name, $value)
	{
		$this->config[$name] = $value;
	}

	/**
	 * Deletes a configuration option.
	 *
	 * @param string $name Option name
	 */
	public function __unset($name)
	{
		unset($this->config[$name]);
	}

	/**
	 * Returns help.
	 *
	 * @return string
	 */
	public static function getHelp()
	{
		return <<<'HELP'
Usage:
	apigen --config <path> [options]
	apigen --source <path> --destination <path> [options]

Options:
	--config|-c        <path>  Config file
	--source|-s        <path>  Source file or directory to parse (can be used multiple times)
	--destination|-d   <path>  Directory where to save the generated documentation
	--exclude          <path>  Exclude file or directory from processing (can be used multiple times)
	--skip-doc-path    <value> Don't generate documentation for classes from this file or directory (can be used multiple times)
	--skip-doc-prefix  <value> Don't generate documentation for classes with this name prefix (can be used multiple times)
	--title            <value> Title of generated documentation
	--base-url         <value> Documentation base URI
	--google-cse       <value> Google Custom Search ID
	--template         <value> Documentation template name
	--template-dir     <path>  Directory with templates
	--allowed-html     <list>  List of allowed HTML tags in documentation, default b,i,a,ul,ol,li,p,br,var,samp,kbd
	--access-levels    <list>  Generate documetation for methods and properties with given access level, default public,protected
	--deprecated       Yes|No  Generate documetation for deprecated classes, methods, properties and constants, default No
	--code             Yes|No  Generate highlighted source code files, default Yes
	--wipeout          Yes|No  Wipe out the destination directory first, default Yes
	--progressbar      On|Off  Display progressbars, default On
	--debug            On|Off  Display additional information in case of an error, default Off
	--help|-h                  Display this help

Only source and destination directories are required - either set explicitly or using a config file.

Files or directories specified by --exclude will not be processed at all.
Classes from files within --skip-doc-path or with --skip-doc-prefix will be parsed but will not have
their documentation generated. However if they have any child classes, the full class tree will be
generated and their inherited methods, properties and constants will be displayed (but will not
be clickable).

Configuration parameters passed via command line have precedence over parameters from a config file.

HELP;
	}
}
