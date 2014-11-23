<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\DI;

use Kdyby\Events\DI\EventsExtension;
use Nette\DI\CompilerExtension;


class ApiGenExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->loadFromFile(__DIR__ . '/apigen.services.neon');
		$this->compiler->parseServices($builder, $config);

		$this->setupTemplating();
	}


	public function beforeCompile()
	{
		$this->setupConsole();
		$this->setupEvents();
		$this->setupTemplatingFilters();
	}


	private function setupTemplating()
	{
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('latteFactory'))
			->setClass('Latte\Engine')
			->addSetup('setTempDirectory', [$builder->expand('%tempDir%/cache/latte')]);
	}


	private function setupConsole()
	{
		$builder = $this->getContainerBuilder();

		$application = $builder->getDefinition($builder->getByType('ApiGen\Console\Application'));
		foreach ($builder->findByType('Symfony\Component\Console\Command\Command') as $command) {
			if ( ! $this->isPhar() && $command === 'ApiGen\Command\SelfUpdateCommand') {
				continue;
			}
			$application->addSetup('add', ['@' . $command]);
		}
	}


	/**
	 * @return bool
	 */
	private function isPhar()
	{
		return substr(__FILE__, 0, 5) === 'phar:';
	}


	private function setupEvents()
	{
		$builder = $this->getContainerBuilder();

		foreach ($builder->findByType('Kdyby\Events\Subscriber') as $event) {
			$builder->getDefinition($event)
				->addTag(EventsExtension::TAG_SUBSCRIBER);
		}
	}


	private function setupTemplatingFilters()
	{
		$builder = $this->getContainerBuilder();

		$latteFactory = $builder->getDefinition($builder->getByType('Latte\Engine'));
		foreach ($builder->findByType('ApiGen\Templating\Filters\Filters') as $filter) {
			$latteFactory->addSetup('addFilter', [NULL, ['@' . $filter, 'loader']]);
		}
	}

}
