<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Console\DI;

use Nette\DI\CompilerExtension;


class ConsoleExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$services = $this->loadFromFile(__DIR__ . '/services.neon');
		$this->compiler->parseServices($builder, $services);
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$builder->prepareClassList();

		$application = $builder->getDefinition($builder->getByType('ApiGen\Console\Application'));
		foreach ($builder->findByType('Symfony\Component\Console\Command\Command') as $definition) {
			if ( ! $this->isPhar() && $definition->getClass() === 'ApiGen\Console\Command\SelfUpdateCommand') {
				continue;
			}
			$application->addSetup('add', ['@' . $definition->getClass()]);
		}
	}


	/**
	 * @return bool
	 */
	private function isPhar()
	{
		return substr(__FILE__, 0, 5) === 'phar:';
	}

}
