<?php

/**
 * ApiGen 2.0 - API documentation generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

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
		'googleCseId' => '',
		'googleCseLabel' => '',
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
		$this->config['templateConfig'] = TEMPLATE_DIR . '/default/config.neon';
	}

	/**
	 * Parses options and configuration.
	 *
	 * @return \ApiGen\Config
	 * @throws \ApiGen\Exception If something in config is wrong
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

			if (is_bool($this->config[$option]) && !is_bool($valueDefinition)) {
				throw new Exception(sprintf('Option %s expects value', $option), Exception::INVALID_CONFIG);
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
	 * @return \ApiGen\Config
	 * @throws \ApiGen\Exception If something in config is wrong
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

		if (!empty($this->config['googleCseId']) && !preg_match('~^\d{21}:[a-z0-9]{11}$~', $this->config['googleCseId'])) {
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
	 * @return \ApiGen\Config
	 * @throws \ApiGen\Exception If something in template config is wrong
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
	public function getHelp()
	{
		return <<<'HELP'
Usage:
	apigen @option@--config@c <@value@path@c> [options]
	apigen @option@--source@c <@value@dir@c|@value@file@c> @option@--destination@c <@value@dir@c> [options]

Options:
	@option@--config@c|@option@-c@c        <@value@file@c>      Config file
	@option@--source@c|@option@-s@c        <@value@dir@c|@value@file@c>  Source file or directory to parse (can be used multiple times)
	@option@--destination@c|@option@-d@c   <@value@dir@c>       Directory where to save the generated documentation
	@option@--exclude@c          <@value@mask@c>      Mask to exclude file or directory from processing (can be used multiple times)
	@option@--skip-doc-path@c    <@value@mask@c>      Don't generate documentation for classes from file or directory with this mask (can be used multiple times)
	@option@--skip-doc-prefix@c  <@value@value@c>     Don't generate documentation for classes with this name prefix (can be used multiple times)
	@option@--main@c             <@value@value@c>     Main project name prefix
	@option@--title@c            <@value@value@c>     Title of generated documentation
	@option@--base-url@c         <@value@value@c>     Documentation base URL
	@option@--google-cse-id@c    <@value@value@c>     Google Custom Search ID
	@option@--google-cse-label@c <@value@value@c>     Google Custom Search label
	@option@--google-analytics@c <@value@value@c>     Google Analytics tracking code
	@option@--template-config@c  <@value@file@c>      Template config file, default "@value@./templates/default/config.neon@c"
	@option@--allowed-html@c     <@value@list@c>      List of allowed HTML tags in documentation, default "@value@b,i,a,ul,ol,li,p,br,var,samp,kbd,tt@c"
	@option@--access-levels@c    <@value@list@c>      Generate documentation for methods and properties with given access level, default "@value@public,protected@c"
	@option@--internal@c         <@value@yes@c|@value@no@c>    Generate documentation for elements marked as internal and display internal documentation parts, default "@value@no@c"
	@option@--php@c              <@value@yes@c|@value@no@c>    Generate documentation for PHP internal classes, default "@value@yes@c"
	@option@--tree@c             <@value@yes@c|@value@no@c>    Generate tree view of classes, interfaces and exceptions, default "@value@yes@c"
	@option@--deprecated@c       <@value@yes@c|@value@no@c>    Generate documentation for deprecated classes, methods, properties and constants, default "@value@no@c"
	@option@--todo@c             <@value@yes@c|@value@no@c>    Generate documentation of tasks, default "@value@no@c"
	@option@--source-code@c      <@value@yes@c|@value@no@c>    Generate highlighted source code files, default "@value@yes@c"
	@option@--undocumented@c     <@value@file@c>      Save a list of undocumented classes, methods, properties and constants into a file
	@option@--wipeout@c          <@value@yes@c|@value@no@c>    Wipe out the destination directory first, default "@value@yes@c"
	@option@--quiet@c            <@value@yes@c|@value@no@c>    Don't display scaning and generating messages, default "@value@no@c"
	@option@--progressbar@c      <@value@yes@c|@value@no@c>    Display progressbars, default "@value@yes@c"
	@option@--debug@c            <@value@yes@c|@value@no@c>    Display additional information in case of an error, default "@value@no@c"
	@option@--help@c|@option@-h@c                      Display this help

Only source and destination directories are required - either set explicitly or using a config file. Configuration parameters passed via command line have precedence over parameters from a config file.

Boolean options (those with possible values @value@yes@c|@value@no@c) do not have to have their values defined explicitly. Using @option@--debug@c and @option@--debug@c=@value@yes@c is exactly the same.

Some options can have multiple values. You can do so either by using them multiple times or by separating values by a comma. That means that writing @option@--source@c=@value@file1.php@c @option@--source@c=@value@file2.php@c or @option@--source@c=@value@file1.php,file2.php@c is exactly the same.

Files or directories specified by @option@--exclude@c will not be processed at all.
Classes from files within @option@--skip-doc-path@c or with @option@--skip-doc-prefix@c will be parsed but will not have their documentation generated. However if they have any child classes, the full class tree will be generated and their inherited methods, properties and constants will be displayed (but will not be clickable).

HELP;
	}
}
