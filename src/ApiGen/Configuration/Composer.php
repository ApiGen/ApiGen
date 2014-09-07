<?php

namespace ApiGen\Configuration;

use ApiGen;
use Nette;
use Nette\DI\CompilerExtension;


/**
 * Composes configuration from all CLI and .neon file
 */
class Composer extends Nette\Object
{
	/** @var Helper */
	private $helper;

	/** @var ApiGen\Console\Helper */
	private $consoleHelper;


	public function __construct()
	{
		$this->consoleHelper = new ApiGen\Console\Helper;
		$this->helper = new ApiGen\Configuration\Helper;
	}


	/**
	 * @param array $config
	 * @return array
	 */
	public function addCliArguments($config)
	{
		// Command line arguments
		$cliArguments = array();
		foreach ($this->consoleHelper->getCliArguments() as $name => $value) {
			$newName = preg_replace_callback( '~-([a-z])~', function($matches) {
				return ucfirst($matches[1]);
			}, $name);

			$cliArguments[$newName] = 1 === count($value) ? $value[0] : $value;
		}

		// Load config file path
		if (empty($cliArguments) && $this->helper->defaultConfigExists()) {
			// Default config file present
			$cliArguments['config'] = $this->helper->getDefaultConfigPath();

		} elseif (isset($cliArguments['config'])) {
			// Make the config file name absolute
			$cliArguments['config'] = $this->helper->getAbsolutePath($cliArguments['config'], array(getcwd()));

		} else {
			throw new \Exception("No '--config' found and no file found in " . $this->helper->getDefaultConfigPath());
		}

		$config = array_merge($config, $cliArguments);
		return $config;
	}


	/**
	 * @param $config
	 * @param CompilerExtension $extension
	 * @return array
	 */
	public function addConfigFileOptions($config, CompilerExtension $extension)
	{
		if ($config['config'] && is_file($config['config'])) {
			$fileConfig = $extension->loadFromFile($config['config']);
			return array_merge($config, $fileConfig);
		}

		return $config;
	}


	/**
	 * @param $config
	 * @param CompilerExtension $extension
	 * @return array
	 */
	public function addTemplateOptions($config, CompilerExtension $extension)
	{
		$templateConfigFile = $this->helper->getAbsolutePath(
			$config['templateConfig'],
			array(
				getcwd(),
				APIGEN_ROOT_PATH . '/src/templates'
			)
		);

		// Merge template configuration
		$config = array_merge_recursive(
			$config,
			array(
				'template' => $extension->loadFromFile($templateConfigFile)
			)
		);

		return $config;
	}

}
