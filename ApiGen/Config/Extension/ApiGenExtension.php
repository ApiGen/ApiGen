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
use Nette\Utils\PhpGenerator\ClassType;

/**
 * Internal ApiGen DIC extension.
 */
final class ApiGenExtension extends CompilerExtension
{
	/**
	 * Prepares internal ApiGen services.
	 */
	public function loadConfiguration()
	{
		// ApiGen config
		$config = $this->containerBuilder->parameters;

		$container = $this->getContainerBuilder();

		// Event dispatcher
		$container->addDefinition($this->prefix('eventDispatcher'))
			->setClass('ApiGen\\EventDispatcher');

		// Memory limit checker
		$container->addDefinition($this->prefix('memoryLimitChecker'))
			->setClass('ApiGen\\MemoryLimitChecker');

		// Application
		$container->addDefinition($this->prefix('application'))
			->setClass('ApiGen\\Application')
			->addSetup('setEventDispatcher');

		// Configuration
		$container->addDefinition($this->prefix('config'))
			->setClass('ApiGen\\Config\\Configuration')
			->addSetup('fillContainer');

		// Logger
		$container->addDefinition('logger')
			->setClass('ApiGen\\ConsoleLogger', array($config['quiet'], $config['colors'], $config['debug']))
			->addSetup('setEventDispatcher');

		// Progressbar
		$container->addDefinition('progressbar')
			->setClass('ApiGen\\ConsoleProgressBar')
			->addSetup('setEventDispatcher');

		// Update checker
		$container->addDefinition($this->prefix('updateChecker'))
			->setClass('ApiGen\\UpdateChecker')
			->addSetup('setEventDispatcher');

		// Error handler
		$container->addDefinition('errorHandler')
			->setClass('ApiGen\\ErrorHandler')
			->addSetup('setEventDispatcher');

		// Charset convertor
		$container->addDefinition($this->prefix('charsetConvertor'))
			->setClass('ApiGen\\CharsetConvertor', array($config['charset']));

		// Source code highlighter
		$container->addDefinition('sourceCodeHighlighter')
			->setClass('ApiGen\\FshlSourceCodeHighlighter');

		// Markup
		$container->addDefinition('markup')
			->setClass('ApiGen\\ParsedownMarkup', array($config['allowedHtml'], '@sourceCodeHighlighter'));

		// Generator
		$container->addDefinition('generator')
			->setClass('ApiGen\\Generator')
			->addSetup('setEventDispatcher');
	}

	/**
	 * Adjusts the generated DI container class.
	 *
	 * @param \Nette\Utils\PhpGenerator\ClassType $class DIC class
	 */
	public function afterCompile(ClassType $class)
	{
		$config = $this->containerBuilder->parameters;

		/**
		 * @var \Nette\Utils\PhpGenerator\Method
		 */
		$initialize = $class->methods['initialize'];

		// Add services as event origins
		foreach ($this->containerBuilder->definitions as $serviceName => $definition) {
			$initialize->addBody('$this->apigen->eventDispatcher->registerOrigin(?, ?);', array($serviceName, strtolower($definition->class)));
		}

		// Register default event listeners
		$initialize->addBody('$this->apigen->eventDispatcher->registerListener("apigen.application", "error", callback($this->errorHandler, "handleException"));');

		if ($config['updateCheck']) {
			$initialize->addBody('$this->apigen->eventDispatcher->registerListener("apigen.application", "startup", callback($this->apigen->updateChecker, "checkUpdate"));');
			$initialize->addBody('$that = $this; $this->apigen->eventDispatcher->registerListener("apigen.updateChecker", "updateAvailable", callback(function(ApiGen\Event $event) use ($that) {
				$that->logger->log("New version %header available\n\n", $event->getPayload());
			}));');
		}

		if ($config['progressbar']) {
			$initialize->addBody('$this->apigen->eventDispatcher->registerListener("generator", "parseStart", callback($this->progressbar, "init"));');
			$initialize->addBody('$this->apigen->eventDispatcher->registerListener("generator", "parseProgress", callback($this->progressbar, "increment"));');
			$initialize->addBody('$this->apigen->eventDispatcher->registerListener("generator", "generateStart", callback($this->progressbar, "init"));');
			$initialize->addBody('$this->apigen->eventDispatcher->registerListener("generator", "generateProgress", callback($this->progressbar, "increment"));');
		}

		$initialize->addBody('$this->apigen->eventDispatcher->registerListener("generator", "parseProgress", callback($this->apigen->memoryLimitChecker, "check"));');
		$initialize->addBody('$this->apigen->eventDispatcher->registerListener("generator", "generateProgress", callback($this->apigen->memoryLimitChecker, "check"));');
	}
}

