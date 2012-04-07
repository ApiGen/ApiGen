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
use ApiGen\Config\Helper;
use ApiGen\ConfigException;
use ApiGen\Environment;
use Nette\Utils\PhpGenerator\ClassType;

/**
 * Internal ApiGen DIC extension.
 */
final class ApiGenExtension extends AbstractExtension
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
		'googleCseLabel' => '',
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
		'debug' => false
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
	 * Configuration helper.
	 *
	 * @var \ApiGen\Config\Helper
	 */
	private $helper;

	/**
	 * Creates an instance of the DIC extension.
	 *
	 * @param array \ApiGen\Config\Helper $helper Configuration helper
	 */
	public function __construct(Helper $helper)
	{
		$this->helper = $helper;

		$this->defaultConfig['colors'] = 'WIN' !== substr(PHP_OS, 0, 3);
		$this->defaultConfig['templateConfig'] = ApiGen\ROOT_PATH . '/templates/' . Helper::DEFAULT_TEMPLATE_CONFIG_FILENAME;
	}

	/**
	 * Prepares internal ApiGen services.
	 */
	public function loadConfiguration()
	{
		// Application configuration
		$config = $this->prepareConfiguration();
		$this->checkConfiguration($config);

		$this->containerBuilder->parameters = $config;

		// Services
		$container = $this->getContainerBuilder();

		// Event dispatcher
		$this->addServiceDefinition($this->prefix('eventDispatcher'), 'ApiGen\\EventDispatcher');

		// Application
		$this->addServiceDefinition($this->prefix('application'), 'ApiGen\\Application')
			->addSetup('setEventDispatcher');

		// Configuration
		$this->addServiceDefinition($this->prefix('config'), 'ApiGen\\Config\\Configuration')
			->addSetup('fillContainer');

		// Logger
		$this->addServiceDefinition('logger', 'ApiGen\\ConsoleLogger', array($config['quiet'], $config['colors'], $config['debug']))
			->addSetup('setEventDispatcher');

		// Progressbar
		$this->addServiceDefinition('progressbar', 'ApiGen\\ConsoleProgressBar')
			->addSetup('setEventDispatcher');

		// Update checker
		$this->addServiceDefinition($this->prefix('updateChecker'), 'ApiGen\\UpdateChecker')
			->addSetup('setEventDispatcher');

		// Error handler
		$this->addServiceDefinition('errorHandler', 'ApiGen\\ErrorHandler')
			->addSetup('setEventDispatcher');

		// Generator
		$this->addServiceDefinition('generator', 'ApiGen\\Generator')
			->addSetup('setEventDispatcher');
	}

	/**
	 * Adjusts the generated DI container class.
	 *
	 * @param \Nette\Utils\PhpGenerator\ClassType $class DIC class
	 */
	public function afterCompile(ClassType $class)
	{
		parent::afterCompile($class);

		$config = $this->containerBuilder->parameters;

		/**
		 * @var \Nette\Utils\PhpGenerator\Method
		 */
		$initialize = $class->methods['initialize'];

		$initialize->addBody('$this->apigen->eventDispatcher->registerListener("apigen.application", "error", callback($this->errorHandler, "handleException"));');

		if ($config['updateCheck']) {
			$initialize->addBody('$this->apigen->eventDispatcher->registerListener("apigen.application", "startup", callback($this->apigen->updateChecker, "checkUpdate"));');
			$initialize->addBody('$that = $this; $this->apigen->eventDispatcher->registerListener("apigen.updateChecker", "updateAvailable", callback(function(ApiGen\Event $event) use ($that) {
				$that->logger->log("New version %h1 available\n\n", $event->getPayload());
			}));');
		}

		// Make the event dispatcher read-only
		$initialize->addBody('$this->apigen->eventDispatcher->freeze();');
	}

	/**
	 * Prepares ApiGen configuration.
	 *
	 * @return array
	 * @throws \ApiGen\ConfigException If there is an error in the configuration
	 */
	private function prepareConfiguration()
	{
		// Command line arguments
		$cliArguments = array();
		$originalNames = array();
		foreach ($this->helper->getCliArguments() as $name => $value) {
			$newName = preg_replace_callback( '~-([a-z])~', function($matches) {
				return ucfirst($matches[1]);
			}, $name);

			$originalNames[$newName] = $name;
			$cliArguments[$newName] = 1 === count($value) ? $value[0] : $value;
		}

		// Compatibility with ApiGen 1.0
		foreach (array('config', 'source', 'destination') as $option) {
			if (isset($cliArguments[$option{0}]) && !isset($cliArguments[$option])) {
				$cliArguments[$option] = $cliArguments[$option{0}];
			}

			unset($cliArguments[$option{0}]);
		}

		// Check for unknown options
		$unknownOptions = array_keys(array_diff_key($cliArguments, $this->defaultConfig));
		if (!empty($unknownOptions)) {
			$originalOptions = array_map(function($option) {
				return (1 === strlen($option) ? '-' : '--') . $option;
			}, array_values(array_diff_key($originalNames, $this->defaultConfig)));

			$message = count($unknownOptions) > 1
				? sprintf('Unknown command line options "%s"', implode('", "', $originalOptions))
				: sprintf('Unknown command line option "%s"', $originalOptions[0]);

			throw new ConfigException($message);
		}

		// Load config file
		if (empty($cliArguments) && $this->helper->defaultConfigExists()) {
			// Default config file present
			$cliArguments['config'] = $this->helper->getDefaultConfigPath();
		} else {
			// Make the config file name absolute
			$cliArguments['config'] = $this->helper->getAbsolutePath($cliArguments['config'], array(getcwd()));
		}
		if (!empty($cliArguments['config']) && is_file($cliArguments['config'])) {
			$fileConfig = $this->loadFromFile($cliArguments['config']);

			$unknownOptions = array_keys(array_diff_key($fileConfig, $this->defaultConfig));
			if (!empty($unknownOptions)) {
				$message = count($unknownOptions) > 1
					? sprintf('Unknown config file options "%s"', implode('", "', $unknownOptions))
					: sprintf('Unknown config file option "%s"', $unknownOptions[0]);

				throw new ConfigException($message);
			}
		} else {
			$fileConfig = array();
		}

		// Merge configurations
		$config = array_merge($this->defaultConfig, $fileConfig, $cliArguments);

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
			} elseif (is_array($valueDefinition)) {
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

			// Check posssible values
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
					$config[$option] = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $value);
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

		// Default template config
		$config['template'] = array(
			'resources' => array(),
			'templates' => array(
				'common' => array(),
				'optional' => array()
			)
		);

		// Merge template configuration
		$config = array_merge_recursive(
			$config,
			array('template' => $this->loadFromFile($this->helper->getAbsolutePath(
				$config['templateConfig'],
				array(
					getcwd(),
					!empty($cliArguments['config']) ? dirname($cliArguments['config']) : '',
					ApiGen\ROOT_PATH . '/templates'
				)
			)))
		);

		// Set overall DIC parameters
		$config['application'] = array(
			'application' => array(
				'name' => 'ApiGen',
				'version' => ApiGen\VERSION
			),
		);

		return $config;
	}

	/**
	 * Checks ApiGen configuration.
	 *
	 * @param array $config Parsed configuration
	 */
	private function checkConfiguration(array $config)
	{
		// @todo
	}
}

