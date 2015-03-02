<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\DI;

use ApiGen\Command\SelfUpdateCommand;
use ApiGen\Console\Application;
use ApiGen\Generator\GeneratorQueue;
use ApiGen\Generator\TemplateGenerator;
use ApiGen\Templating\Filters\Filters;
use Latte\Engine;
use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;


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
			->setClass(Engine::class)
			->addSetup('setTempDirectory', [$builder->expand('%tempDir%/cache/latte')]);
	}


	private function setupConsole()
	{
		$builder = $this->getContainerBuilder();

		$application = $builder->getDefinition($builder->getByType(Application::class));
		foreach ($builder->findByType(Command::class) as $definition) {
			if ( ! $this->isPhar() && $definition->getClass() === SelfUpdateCommand::class) {
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
		foreach ($builder->findByType(Filters::class) as $definition) {
			$latteFactory->addSetup('addFilter', [NULL, ['@' . $definition->getClass(), 'loader']]);
		}
	}


	private function setupGeneratorQueue()
	{
		$builder = $this->getContainerBuilder();
		$generator = $builder->getDefinition($builder->getByType(GeneratorQueue::class));
		foreach ($builder->findByType(TemplateGenerator::class) as $definition) {
			$generator->addSetup('addToQueue', ['@' . $definition->getClass()]);
		}
	}

}
