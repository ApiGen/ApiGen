<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;


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
		$this->setupTemplatingFilters();
		$this->setupGeneratorQueue();
		$this->setupConsole();
	}


	private function loadServicesFromConfig()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->loadFromFile(__DIR__ . '/services.neon');
		$this->compiler->parseServices($builder, $config);
	}


	private function setupTemplating()
	{
		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('latteFactory'))
			->setClass('Latte\Engine')
			->addSetup('setTempDirectory', [$builder->expand('%tempDir%/cache/latte')]);
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


	private function setupConsole()
	{
		$builder = $this->getContainerBuilder();

//		$parser = $builder->getDefinition($builder->getByType('ApiGen\Parser\Parser'));
//		$parser->setImplement('ApiGen\Parser\Parser');

		$generator = $builder->getDefinition($builder->getByType('ApiGen\Generator\GeneratorQueue'));
		$generator->setClass('ApiGen\Console\Bridges\ApiGenGenerators\GeneratorQueueInterface');
	}

}
