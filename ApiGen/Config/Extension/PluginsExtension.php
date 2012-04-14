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

use Nette\Config\CompilerExtension;
use Nette\InvalidStateException;
use Nette\Loaders\RobotLoader;
use Nette\Utils\PhpGenerator\ClassType;
use Nette\Utils\PhpGenerator\PhpLiteral;
use ReflectionClass;

/**
 * ApiGen plugins DIC extension.
 */
final class PluginsExtension extends CompilerExtension
{
	/**
	 * Event listener definition format.
	 *
	 * @var string
	 */
	const EVENT_LISTENER_FORMAT = '~^([a-z][a-z0-9_]+)@(.*?)::([a-z0-9]+)$~i';

	/**
	 * Robotloader instance.
	 *
	 * @var \Nette\Loaders\RobotLoader
	 */
	private $loader;

	/**
	 * Creates the DIC extension.
	 *
	 * @param \Nette\Loaders\RobotLoader $loader Robotloader instance
	 */
	public function __construct(RobotLoader $loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Initializes the plugin autoloader.
	 */
	public function beforeCompile()
	{
		foreach ($this->containerBuilder->parameters['plugins'] as $definition) {
			$this->loader->addDirectory($definition['location']);
		}

		$this->loader->register();
	}

	/**
	 * Prepares internal ApiGen services.
	 */
	public function loadConfiguration()
	{
		$container = $this->getContainerBuilder();

		foreach ($this->containerBuilder->parameters['plugins'] as $name => $definition) {
			$name = $this->processPluginName($name);

			// Only internal services
			$autoWired = false === strpos($name, '.') && $container->hasDefinition($name);

			// In case we are overriding an internal service
			$container->removeDefinition($name);

			$service = $container->addDefinition($name);
			$service
				->setClass($definition['class'])
				->setAutowired($autoWired);
			if (!empty($definition['options'])) {
				$service->addSetup('setOptions', array('options' => $definition['options']));
			}
		}
	}

	/**
	 * Adjusts the generated DI container class.
	 *
	 * @param \Nette\Utils\PhpGenerator\ClassType $class DIC class
	 */
	public function afterCompile(ClassType $class)
	{
		/**
		 * @var \Nette\Utils\PhpGenerator\Method
		 */
		$initialize = $class->methods['initialize'];

		// Plugin event listeners
		foreach ($this->containerBuilder->parameters['plugins'] as $name => $definition) {
			$reflection = new ReflectionClass($definition['class']);
			if (!$reflection->implementsInterface('ApiGen\\IPlugin')) {
				throw new InvalidStateException(sprintf('Plugin "%s" does not implement interface ApiGen\\IPlugin', $name));
			}

			if (!empty($definition['events'])) {
				foreach ($definition['events'] as $eventDefinition) {
					preg_match(self::EVENT_LISTENER_FORMAT, $eventDefinition, $matches);

					$initialize->addBody(
						'$this->apigen->eventDispatcher->registerListener(?, ?, callback(?, ?));',
						array($matches[2], $matches[3], new PhpLiteral('$this->' . str_replace('.', '->', $this->processPluginName($name))), $matches[1])
					);
				}
			}
		}

		// Make the event dispatcher read-only
		$initialize->addBody('$this->apigen->eventDispatcher->freeze();');
	}

	/**
	 * Processes a plugin name to make it DIC compatible.
	 *
	 * @param string $name Original plugin name
	 * @return string
	 */
	private function processPluginName($name)
	{
		if (0 === strpos($name, $this->name . '.')) {
			list($prefix, $name) = explode('.', $name, 2);
		} else {
			$prefix = null;
		}

		$name = preg_replace_callback('~[^a-z0-9]+([a-z0-9])~i', function($matches) {
			return ucfirst($matches[1]);
		}, $name);

		return ($prefix ? $prefix . '.' : '') . $name;
	}
}
