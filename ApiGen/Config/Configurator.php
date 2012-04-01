<?php

namespace ApiGen\Config;

use ApiGen\Config\Extension\ApiGenExtension;
use ApiGen\Config\Extension\PluginsExtension;
use Nette\Caching\Storages\MemoryStorage;
use Nette\Config\Configurator as NetteConfigurator;
use Nette\Config\Compiler;
use Nette\Loaders\RobotLoader;
use Nette\Utils\LimitedScope;

/**
 * ApiGen configurator.
 *
 * Parses config files and creates internal configuration structures.
 */
class Configurator extends NetteConfigurator
{
	/**
	 * Config cache.
	 *
	 * @var \Nette\Caching\Storages\MemoryStorage
	 */
	private $cache;

	/**
	 * Autoloader.
	 *
	 * @var \Nette\Loaders\RobotLoader
	 */
	private $loader;

	/**
	 * Configuration helper.
	 *
	 * @var \ApiGen\Config\Helper
	 */
	private $helper;

	/**
	 * Creates the configurator instance and prepares configuration cache.
	 *
	 * @param array \ApiGen\Config\Helper $helper Configuration helper
	 */
	public function __construct(Helper $helper)
	{
		parent::__construct();

		$this->cache = new MemoryStorage();

		$this->loader = new RobotLoader();
		$this->loader->setCacheStorage($this->cache);
		$this->loader->autoRebuild = false;

		$this->helper = $helper;
	}

	/**
	 * Returns the autoloader instance.
	 *
	 * @return \Nette\Loaders\RobotLoader
	 */
	public function createRobotLoader()
	{
		return $this->loader;
	}

	/**
	 * Returns the application DIC.
	 *
	 * @return \SystemContainer
	 */
	public function createContainer()
	{
		LimitedScope::evaluate($c = $this->buildContainer());

		$container = new $this->parameters['container']['class'];
		$container->initialize();

		return $container;
	}

	/**
	 * Creates the DIC compiler.
	 *
	 * @return \Nette\Config\Compiler
	 */
	protected function createCompiler()
	{
		$compiler = new Compiler();
		$compiler->addExtension('apigen', new ApiGenExtension($this->helper));
		$compiler->addExtension('plugins', new PluginsExtension());

		return $compiler;
	}
}
