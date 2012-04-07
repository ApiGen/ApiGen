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
use Nette\Utils\PhpGenerator\ClassType;

/**
 * Base class of ApiGen DIC extensions.
 */
abstract class AbstractExtension extends CompilerExtension
{
	/**
	 * List of registered services.
	 *
	 * @var array
	 */
	private $services = array();

	/**
	 * Adds a service definition.
	 *
	 * @param string $serviceName Service name
	 * @param string $className Class name
	 * @param array $args Service constructor arguments
	 * @return \Nette\DI\ServiceDefinition
	 */
	final protected function addServiceDefinition($serviceName, $className, array $args = array())
	{
		$definition = $this->getContainerBuilder()->addDefinition($serviceName)->setClass($className, $args);

		$this->services[$serviceName] = $className;

		return $definition;
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

		foreach ($this->services as $serviceName => $className) {
			$initialize->addBody('$this->apigen->eventDispatcher->registerOrigin(?, ?);', array($serviceName, $className));
		}
	}
}
