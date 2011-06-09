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

/**
 * Configuration processing class.
 *
 * @author Jaroslav Hanslík
 * @author Ondřej Nešpor
 */
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
		'main' => '',
		'title' => '',
		'baseUrl' => '',
		'googleCse' => '',
		'googleAnalytics' => '',
		'templateConfig' => '',
		'allowedHtml' => array('b', 'i', 'a', 'ul', 'ol', 'li', 'p', 'br', 'var', 'samp', 'kbd', 'tt'),
		'accessLevels' => array('public', 'protected'),
		'internal' => false,
		'php' => true,
		'tree' => true,
		'deprecated' => false,
		'todo' => false,
		'sourceCode' => true,
		'undocumented' => '',
		'wipeout' => true,
		'quiet' => false,
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
		'templateConfig'
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
	 */
	public function __construct()
	{
		$options = $_SERVER['argv'];
		array_shift($options);

		while ($option = current($options)) {
			if (preg_match('~^--([a-z][-a-z]*[a-z])(?:=(.+))?$~', $option, $matches) || preg_match('~^-([a-z])=?(.*)~', $option, $matches)) {
				$name = $matches[1];

				if (!empty($matches[2])) {
					$value = $matches[2];
				} else {
					$next = next($options);
					if (false === $next || '-' === $next{0}) {
						prev($options);
						$value = true;
					} else {
						$value = $next;
					}
				}

				$this->options[$name][] = $value;
			}

			next($options);
		}
		$this->options = array_map(function($value) {
			return 1 === count($value) ? $value[0] : $value;
		}, $this->options);

		$this->config = self::$defaultConfig;
		$this->config['templateConfig'] = realpath(__DIR__ . '/../../templates/default/config.neon');
	}

	/**
	 * Parses options and configuration.
	 *
	 * @return \Apigen\Config
	 */
	public function parse()
	{
		// Compatibility with ApiGen 1.0
		foreach (array('config', 'source', 'destination') as $option) {
			if (isset($this->options[$option{0}]) && !isset($this->options[$option])) {
				$this->options[$option] = $this->options[$option{0}];
			}
			unset($this->options[$option{0}]);
		}

		// Config file
		if (isset($this->options['config']) && is_file($this->options['config'])) {
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
			if (is_array($this->config[$option]) && !is_array($valueDefinition)) {
				throw new Exception(sprintf('Option %s must be set only once', $option), Exception::INVALID_CONFIG);
			}

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
				usort($this->config[$option], 'strcasecmp');
			} else {
				if (file_exists($this->config[$option])) {
					$this->config[$option] = realpath($this->config[$option]);
				}
			}
		}

		// Unify directory separators
		foreach (array('exclude', 'skipDocPath') as $option) {
			$this->config[$option] = array_map(function($mask) {
				return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $mask);
			}, $this->config[$option]);
			usort($this->config[$option], 'strcasecmp');
		}

		// Unify prefixes
		$this->config['skipDocPrefix'] = array_map(function($prefix) {
			return ltrim($prefix, '\\');
		}, $this->config['skipDocPrefix']);
		usort($this->config['skipDocPrefix'], 'strcasecmp');

		// Base url without slash at the end
		$this->config['baseUrl'] = rtrim($this->config['baseUrl'], '/');

		// No progressbar in quiet mode
		if ($this->config['quiet']) {
			$this->config['progressbar'] = false;
		}

		// Check
		$this->check();

		// Default template config
		$this->config['resources'] = array();
		$this->config['templates'] = array('common' => array(), 'optional' => array());

		// Merge template config
		$this->config = array_merge_recursive($this->config, Neon::decode(file_get_contents($this->config['templateConfig'])));

		// Check template
		$this->checkTemplate();

		return $this;
	}

	/**
	 * Checks configuration.
	 *
	 * @return \Apigen\Config
	 */
	private function check()
	{
		if (!empty($this->config['config']) && !is_file($this->config['config'])) {
			throw new Exception(sprintf('Config file %s doesn\'t exist', $this->config['config']), Exception::INVALID_CONFIG);
		}

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

		if (!is_file($this->config['templateConfig'])) {
			throw new Exception('Template config doesn\'t exist', Exception::INVALID_CONFIG);
		}

		if (!empty($this->config['baseUrl']) && !preg_match('~^https?://(?:[-a-z0-9]+\.)+[a-z]{2,6}(?:/.*)?$~i', $this->config['baseUrl'])) {
			throw new Exception('Invalid base url', Exception::INVALID_CONFIG);
		}

		if (!empty($this->config['googleCse']) && !preg_match('~^\d{21}:[a-z0-9]{11}$~', $this->config['googleCse'])) {
			throw new Exception('Invalid Google Custom Search ID', Exception::INVALID_CONFIG);
		}

		if (!empty($this->config['googleAnalytics']) && !preg_match('~^UA\\-\\d+\\-\\d+$~', $this->config['googleAnalytics'])) {
			throw new Exception('Invalid Google Analytics tracking code', Exception::INVALID_CONFIG);
		}

		if (empty($this->config['accessLevels'])) {
			throw new Exception('No supported access level given', Exception::INVALID_CONFIG);
		}

		return $this;
	}

	/**
	 * Checks template configuration.
	 *
	 * @return \Apigen\Config
	 */
	private function checkTemplate()
	{
		foreach (array('main', 'optional') as $section) {
			foreach ($this->config['templates'][$section] as $type => $config) {
				if (!isset($config['filename'])) {
					throw new Exception(sprintf('Filename for %s is not defined', $type), Exception::INVALID_CONFIG);
				}
				if (!isset($config['template'])) {
					throw new Exception(sprintf('Template for %s is not defined', $type), Exception::INVALID_CONFIG);
				}
				if (!is_file(dirname($this->config['templateConfig']) . DIRECTORY_SEPARATOR . $config['template'])) {
					throw new Exception(sprintf('Template for %s doesn\'t exist', $type), Exception::INVALID_CONFIG);
				}
			}
		}

		return $this;
	}

	/**
	 * Checks if a configuration option exists.
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
	 * If the user requests help.
	 *
	 * @return boolean
	 */
	public function isHelpRequested()
	{
		return empty($this->options) || isset($this->options['h']) || isset($this->options['help']);
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
	apigen --source <dir|file> --destination <dir> [options]

Options:
	--config|-c        <file>      Config file
	--source|-s        <dir|file>  Source file or directory to parse (can be used multiple times)
	--destination|-d   <dir>       Directory where to save the generated documentation
	--exclude          <mask>      Mask to exclude file or directory from processing (can be used multiple times)
	--skip-doc-path    <mask>      Don't generate documentation for classes from file or directory with this mask (can be used multiple times)
	--skip-doc-prefix  <value>     Don't generate documentation for classes with this name prefix (can be used multiple times)
	--main             <value>     Main project name prefix
	--title            <value>     Title of generated documentation
	--base-url         <value>     Documentation base URL
	--google-cse       <value>     Google Custom Search ID
	--google-analytics <value>     Google Analytics tracking code
	--template-config  <file>      Template config file, default "./templates/default/config.neon"
	--allowed-html     <list>      List of allowed HTML tags in documentation, default "b,i,a,ul,ol,li,p,br,var,samp,kbd,tt"
	--access-levels    <list>      Generate documentation for methods and properties with given access level, default "public,protected"
	--internal         <yes|no>    Generate documentation for elements marked as internal, default "no"
	--php              <yes|no>    Generate documentation for PHP internal classes, default "yes"
	--tree             <yes|no>    Generate tree view of classes, interfaces and exceptions, default "yes"
	--deprecated       <yes|no>    Generate documentation for deprecated classes, methods, properties and constants, default "no"
	--todo             <yes|no>    Generate documentation of tasks, default "no"
	--source-code      <yes|no>    Generate highlighted source code files, default "yes"
	--undocumented     <file>      Save a list of undocumented classes, methods, properties and constants into a file
	--wipeout          <yes|no>    Wipe out the destination directory first, default "yes"
	--quiet            <yes|no>    Don't display scaning and generating messages, default "no"
	--progressbar      <yes|no>    Display progressbars, default "yes"
	--debug            <yes|no>    Display additional information in case of an error, default "no"
	--help|-h                      Display this help

Only source and destination directories are required - either set explicitly or using a config file. Configuration parameters passed via command line have precedence over parameters from a config file.

Boolean options (those with possible values yes|no) do not have to have their values defined explicitly. Using --debug and --debug=yes is exactly the same.

Some options can have multiple values. You can do so either by using them multiple times or by separating values by a comma. That means that writing --source=file1.php --source=file2.php or --source=file1.php,file2.php is exactly the same.

Files or directories specified by --exclude will not be processed at all.
Classes from files within --skip-doc-path or with --skip-doc-prefix will be parsed but will not have their documentation generated. However if they have any child classes, the full class tree will be generated and their inherited methods, properties and constants will be displayed (but will not be clickable).

HELP;
	}
}
