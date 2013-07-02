<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen\Config\Extension;

use ApiGen;
use ApiGen\Config\Exception as ConfigException;
use ApiGen\Config\Helper;
use ApiGen\Environment;
use Nette\Config\CompilerExtension;

/**
 * ApiGen configuration DIC extension.
 */
final class ConfigExtension extends CompilerExtension
{
	/**
	 * Default configuration.
	 *
	 * @var array
	 */
	private $defaultConfig = array(
		'config' => '',
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
		'sourceCode' => true,
		'report' => '',
		'undocumented' => '',
		'wipeout' => true,
		'quiet' => false,
		'progressbar' => true,
		'colors' => true,
		'updateCheck' => true,
		'debug' => false,
		'help' => false,
		'pluginConfig' => array(),
		'plugins' => array()
	);

	/**
	 * File or directory path options.
	 *
	 * @var array
	 */
	private $pathOptions = array(
		'config',
		'source',
		'destination',
		'templateConfig',
		'pluginConfig',
		'report',
		'exclude',
		'skipDocPath'
	);

	/**
	 * Possible values for options.
	 *
	 * @var array
	 */
	private $possibleOptionsValues = array(
		'groups' => array('auto', 'namespaces', 'packages', 'none'),
		'autocomplete' => array('classes', 'constants', 'functions', 'methods', 'properties', 'classconstants'),
		'accessLevels' => array('public', 'protected', 'private')
	);

	/**
	 * CLI arguments.
	 *
	 * @var array
	 */
	private $arguments;

	/**
	 * Creates an instance of the DIC extension.
	 *
	 * @param array $arguments CLI arguments
	 */
	public function __construct(array $arguments)
	{
		$this->arguments = $arguments;

		$this->defaultConfig['colors'] = Environment::isTerminalWithColors();
		$this->defaultConfig['templateConfig'] = Environment::getRootDir() . '/templates/' . Helper::DEFAULT_TEMPLATE_CONFIG_FILENAME;
	}

	/**
	 * Prepares ApiGen configuration.
	 */
	public function loadConfiguration()
	{
		$config = $this->prepareConfiguration();
		$this->checkConfiguration($config);

		$this->containerBuilder->parameters = $config;
	}

	/**
	 * Prepares ApiGen configuration.
	 *
	 * @return array
	 * @throws \ApiGen\Config\Exception If there is an error in the configuration
	 */
	private function prepareConfiguration()
	{
		// Short command line options
		$cliArguments = $this->arguments;
		foreach (array('config', 'source', 'destination', 'help') as $option) {
			if (isset($cliArguments[$option{0}]) && !isset($cliArguments[$option])) {
				$cliArguments[$option] = $cliArguments[$option{0}];
			}

			unset($cliArguments[$option{0}]);
		}

		// Command line options
		$cliOptions = array();
		$originalNames = array();
		foreach ($cliArguments as $name => $value) {
			$newName = preg_replace_callback('~-([a-z])~', function($matches) {
				return ucfirst($matches[1]);
			}, $name);

			$originalNames[$newName] = $name;
			$cliOptions[$newName] = $value;
		}

		// Check for unknown options
		$unknownOptions = array_keys(array_diff_key($cliOptions, $this->defaultConfig));
		if (!empty($unknownOptions)) {
			$originalOptions = array_map(function($option) {
				return (1 === strlen($option) ? '-' : '--') . $option;
			}, array_values(array_diff_key($originalNames, $this->defaultConfig)));

			$message = count($unknownOptions) > 1
				? sprintf('Unknown command line options "%s"', implode('", "', $originalOptions))
				: sprintf('Unknown command line option "%s"', $originalOptions[0]);

			throw new ConfigException($message);
		}

		// It is not possible to define plugins via command line
		if (isset($cliOptions['plugins'])) {
			throw new ConfigException('Plugins cannot be defined via command line options');
		}

		// Load config file
		if (empty($cliOptions) && Helper::defaultConfigExists()) {
			// Default config file present
			$cliOptions['config'] = Helper::getDefaultConfigPath();
		} elseif (!empty($cliOptions['config'])) {
			// Make the config file name absolute
			$cliOptions['config'] = Helper::getAbsoluteFilePath($cliOptions['config'], array(getcwd()));

			if (null === $cliOptions['config']) {
				// @todo
			}
		}
		if (!empty($cliOptions['config'])) {
			$fileOptions = $this->loadFromFile($cliOptions['config']);

			$unknownOptions = array_keys(array_diff_key($fileOptions, $this->defaultConfig));
			if (!empty($unknownOptions)) {
				$message = count($unknownOptions) > 1
					? sprintf('Unknown config file options "%s"', implode('", "', $unknownOptions))
					: sprintf('Unknown config file option "%s"', $unknownOptions[0]);

				throw new ConfigException($message);
			}
		} else {
			$fileOptions = array();
		}

		// Merge configurations
		$config = array_merge($this->defaultConfig, $fileOptions, $cliOptions);

		// Compatibility with the old option name "undocumented"
		if (!isset($config['report']) && isset($config['undocumented'])) {
			$config['report'] = $config['undocumented'];
			unset($config['undocumented']);
		}

		// Convert option data types
		foreach ($this->defaultConfig as $option => $valueDefinition) {
			if (is_array($config[$option]) && !is_array($valueDefinition)) {
				throw new ConfigException(sprintf('Option "%s" must be set only once', $option));
			}

			if (is_bool($config[$option]) && !is_bool($valueDefinition)) {
				throw new ConfigException(sprintf('Option "%s" expects value', $option));
			}

			if (!is_bool($config[$option]) && is_bool($valueDefinition)) {
				// Boolean option
				$value = strtolower($config[$option]);
				if ('on' === $value || 'yes' === $value || 'true' === $value || '' === $value) {
					$value = true;
				} elseif ('off' === $value || 'no' === $value || 'false' === $value) {
					$value = false;
				}

				$config[$option] = (bool) $value;
			} elseif (is_array($valueDefinition) && 'plugins' !== $option) {
				// Array option
				$config[$option] = array_unique((array) $config[$option]);
				foreach ($config[$option] as $key => $value) {
					$value = explode(',', $value);
					while (!empty($value)) {
						array_push($config[$option], array_shift($value));
					}
					$config[$option][$key] = array_shift($value);
				}
				$config[$option] = array_values(array_filter($config[$option]));
			}

			// Check possible values
			if (!empty($this->possibleOptionsValues[$option])) {
				$values = $this->possibleOptionsValues[$option];

				if (is_array($valueDefinition)) {
					$config[$option] = array_filter($config[$option], function($value) use ($values) {
						return in_array($value, $values);
					});
				} elseif (!in_array($config[$option], $values)) {
					$config[$option] = '';
				}
			}
		}

		// Unify character sets
		$config['charset'] = array_map('strtoupper', $config['charset']);

		// Process options that specify a filesystem path
		foreach ($this->pathOptions as $option) {
			if (is_array($config[$option])) {
				array_walk($config[$option], function(&$value) {
					if (file_exists($value)) {
						$value = realpath($value);
					} else {
						$value = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $value);
					}
				});
				usort($config[$option], 'strcasecmp');
			} else {
				if (file_exists($config[$option])) {
					$config[$option] = realpath($config[$option]);
				} else {
					$config[$option] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $config[$option]);
				}
			}
		}

		// Unify prefixes
		$config['skipDocPrefix'] = array_map(function($prefix) {
			return ltrim($prefix, '\\');
		}, $config['skipDocPrefix']);
		usort($config['skipDocPrefix'], 'strcasecmp');

		// Base url without slash at the end
		$config['baseUrl'] = rtrim($config['baseUrl'], '/');

		// No progressbar in quiet mode
		if ($config['quiet']) {
			$config['progressbar'] = false;
		}

		// No update check in debug mode
		if ($config['debug']) {
			$config['updateCheck'] = false;
		}

		// Help
		if (empty($cliOptions) && !Helper::defaultConfigExists()) {
			$config['help'] = true;
		}

		// Default template config
		$config['template'] = array(
			'require' => array(),
			'resources' => array(),
			'templates' => array(
				'common' => array(),
				'optional' => array()
			)
		);

		// Merge template configuration
		$templateConfigAbsolutePath = Helper::getAbsoluteFilePath(
			$config['templateConfig'],
			array(
				getcwd(),
				!empty($cliArguments['config']) ? dirname($cliArguments['config']) : '',
				Helper::getTemplatesDir()
			)
		);

		if (null !== $templateConfigAbsolutePath) {
			// @todo
		}

		$config = array_merge_recursive(
			$config,
			array('template' => $this->loadFromFile($templateConfigAbsolutePath))
		);

		// Plugins
		foreach ($config['pluginConfig'] as $fileName) {
			$fileName = Helper::getAbsoluteFilePath(
				$fileName,
				array(
					getcwd(),
					!empty($cliArguments['config']) ? dirname($cliArguments['config']) : ''
				)
			);

			// @todo Check if file name is not null

			foreach ($this->loadFromFile($fileName) as $name => $definition) {
				if (isset($definition['location'])) {
					$definition['location'] = Helper::getAbsoluteDirectoryPath(
						$definition['location'],
						array(dirname($fileName))
					);
				}

				$config['plugins'][$name] = $definition;
			}
		}

		// Set overall DIC parameters
		$config['application'] = array(
			'application' => array(
				'name' => Environment::getApplicationName(),
				'version' => Environment::getApplicationVersion()
			),
		);

		return $config;
	}

	/**
	 * Checks ApiGen configuration.
	 *
	 * @param array $config Parsed configuration
	 * @throws \ApiGen\Config\Exception If there is an error in configuration
	 */
	private function checkConfiguration(array $config)
	{
		// Base configuration
		if (empty($config['source'])) {
			throw new ConfigException('Source is not set');
		}
		foreach ($config['source'] as $source) {
			if (!file_exists($source)) {
				throw new ConfigException(sprintf('Source "%s" doesn\'t exist', $source));
			}
		}

		if (empty($config['destination'])) {
			throw new ConfigException('Destination is not set');
		}

		foreach ($config['extensions'] as $extension) {
			if (!preg_match('~^[a-z\\d]+$~i', $extension)) {
				throw new ConfigException(sprintf('Invalid file extension "%s"', $extension));
			}
		}

		if (!empty($config['googleCseId']) && !preg_match('~^\d{21}:[-a-z0-9_]{11}$~', $config['googleCseId'])) {
			throw new ConfigException(sprintf('Invalid Google Custom Search ID "%s"', $config['googleCseId']));
		}

		if (!empty($config['googleAnalytics']) && !preg_match('~^UA\\-\\d+\\-\\d+$~', $config['googleAnalytics'])) {
			throw new ConfigException(sprintf('Invalid Google Analytics tracking code "%s"', $config['googleAnalytics']));
		}

		if (empty($config['groups'])) {
			throw new ConfigException('No supported groups value given');
		}

		if (empty($config['autocomplete'])) {
			throw new ConfigException('No supported autocomplete value given');
		}

		if (empty($config['accessLevels'])) {
			throw new ConfigException('No supported access level given');
		}

		// Template configuration
		$require = $config['template']['require'];
		if (isset($require['min']) && !preg_match('~^\\d+(?:\\.\\d+){0,2}$~', $require['min'])) {
			throw new ConfigException(sprintf('Invalid minimal version definition "%s"', $require['min']));
		}
		if (isset($require['max']) && !preg_match('~^\\d+(?:\\.\\d+){0,2}$~', $require['max'])) {
			throw new ConfigException(sprintf('Invalid maximal version definition "%s"', $require['max']));
		}

		$isMinOk = function($min) {
			$min .= str_repeat('.0', 2 - substr_count($min, '.'));
			return version_compare($min, Environment::getApplicationVersion(), '<=');
		};
		$isMaxOk = function($max) {
			$max .= str_repeat('.0', 2 - substr_count($max, '.'));
			return version_compare($max, Environment::getApplicationVersion(), '>=');
		};

		if (isset($require['min'], $require['max']) && (!$isMinOk($require['min']) || !$isMaxOk($require['max']))) {
			throw new ConfigException(sprintf('The template requires version from "%s" to "%s", you are using version "%s"', $require['min'], $require['max'], Environment::getApplicationVersion()));
		} elseif (isset($require['min']) && !$isMinOk($require['min'])) {
			throw new ConfigException(sprintf('The template requires version "%s" or newer, you are using version "%s"', $require['min'], Environment::getApplicationVersion()));
		} elseif (isset($require['max']) && !$isMaxOk($require['max'])) {
			throw new ConfigException(sprintf('The template requires version "%s" or older, you are using version "%s"', $require['max'], Environment::getApplicationVersion()));
		}

		foreach (array('main', 'optional') as $section) {
			foreach ($config['template']['templates'][$section] as $type => $typeConfig) {
				if (!isset($typeConfig['filename'])) {
					throw new ConfigException(sprintf('Filename for "%s" is not defined', $type));
				}
				if (!isset($typeConfig['template'])) {
					throw new ConfigException(sprintf('Template for "%s" is not defined', $type));
				}
				if (null === Helper::getAbsoluteFilePath($typeConfig['template'], array(dirname($config['templateConfig'])))) {
					throw new ConfigException(sprintf('Template for "%s" doesn\'t exist', $type));
				}
			}
		}

		// Plugins configuration
		foreach ($config['plugins'] as $pluginName => $definition) {
			if (!is_array($definition)) {
				throw new ConfigException(sprintf('Definition of plugin "%s" has to be an array', $pluginName));
			}

			if (!isset($definition['location'], $definition['class'])) {
				throw new ConfigException(sprintf('Plugin "%s" has to declare its location and class name', $pluginName));
			}

			foreach ($definition as $key => $value) {
				switch ($key) {
					case 'location':
					case 'class':
						if (!is_string($value)) {
							throw new ConfigException(sprintf('Parameter "%s" value has to be a string in plugin "%s" configuration', $key, $pluginName));
						}

						if ('location' === $key && !is_dir($value)) {
							throw new ConfigException(sprintf('Plugin "%s" location "%s" does not exist', $pluginName, $value));
						}

						break;

					case 'events':
						if (!is_array($value)) {
							throw new ConfigException(sprintf('Event hooks have to be defined as an array in plugin "%s" configuration', $pluginName));
						}

						foreach ($value as $index => $listenerDefinition) {
							if (!preg_match(PluginsExtension::EVENT_LISTENER_FORMAT, $listenerDefinition, $matches)) {
								throw new ConfigException(sprintf('Event hooks #%d definition is invalid in plugin "%s" configuration', $index + 1, $pluginName));
							}
						}

						break;

					case 'options':
						if (!is_array($value)) {
							throw new ConfigException(sprintf('Parameter "%s" value has to be an array in plugin "%s" configuration', $key, $pluginName));
						}

						break;

					default:
						throw new ConfigException(sprintf('Unknown plugin configuration option "%s" in plugin "%s" configuration', $key, $pluginName));
				}
			}
		}
	}
}
