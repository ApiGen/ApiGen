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

		// Make the event dispatcher read-only
		$initialize->addBody('$this->apigen->eventDispatcher->freeze();');
	}
}
