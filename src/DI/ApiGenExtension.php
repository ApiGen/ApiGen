<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\DI;

use Nette\DI\CompilerExtension;


class ApiGenExtension extends CompilerExtension
{

	public function loadConfiguration()
	{
		$this->loadServicesFromConfig();
		$this->setupTemplating();
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$builder->prepareClassList();
		$this->setupConsole();
		$this->setupTemplatingFilters();
		$this->setupGeneratorQueue();
	}


	private function loadServicesFromConfig()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->loadFromFile(__DIR__ . '/apigen.services.neon');
		$this->compiler->parseServices($builder, $config);
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

		foreach ($builder->findByType('Symfony\Component\Console\Command\Command') as $definition) {
			if ( ! $this->isPhar() && $definition->getClass() === 'ApiGen\Command\SelfUpdateCommand') {
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


	private function setupTemplatingFilters()
	{
		$builder = $this->getContainerBuilder();
		$latteFactory = $builder->getDefinition($builder->getByType('Latte\Engine'));
		foreach ($builder->findByType('ApiGen\Templating\Filters\Filters') as $definition) {
			$latteFactory->addSetup('addFilter', [NULL, ['@' . $definition->getClass(), 'loader']]);
		}
	}


	private function setupGeneratorQueue()
	{
		$builder = $this->getContainerBuilder();
		$generator = $builder->getDefinition($builder->getByType('ApiGen\Generator\GeneratorQueue'));
		foreach ($builder->findByType('ApiGen\Generator\TemplateGenerator') as $definition) {
			$generator->addSetup('addToQueue', ['@' . $definition->getClass()]);
		}
	}

}
