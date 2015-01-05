<?php

namespace ApiGen\Tests\DI;

use ApiGen\DI\ApiGenExtension;
use ApiGen\Tests\MethodInvoker;
use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit_Framework_TestCase;


class ApplicationTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @return ApiGenExtension
	 */
	public function testLoadServicesFromConfig()
	{
		$extension = $this->getExtension();
		MethodInvoker::callMethodOnObject($extension, 'loadServicesFromConfig');

		$builder = $extension->getContainerBuilder();
		$builder->prepareClassList();

		$definition = $builder->getDefinition($builder->getByType('ApiGen\Generator\GeneratorQueue'));
		$this->assertSame('ApiGen\Generator\GeneratorQueue', $definition->getClass());

		$definition = $builder->getDefinition($builder->getByType('ApiGen\Generator\Resolvers\ElementResolver'));
		$this->assertSame('ApiGen\Generator\Resolvers\ElementResolver', $definition->getClass());

		return $extension;
	}


	public function testSetupTemplating()
	{
		$extension = $this->getExtension();
		MethodInvoker::callMethodOnObject($extension, 'setupTemplating');

		$builder = $extension->getContainerBuilder();
		$builder->prepareClassList();

		$definition = $builder->getDefinition($builder->getByType('Latte\Engine'));
		$this->assertSame('Latte\Engine', $definition->getClass());

		$this->assertSame(
			__DIR__ . '/../temp/cache/latte',
			$definition->getSetup()[0]->arguments[0]
		);
	}


	/**
	 * @depends testLoadServicesFromConfig
	 */
	public function testSetupConsole(ApiGenExtension $extension)
	{
		MethodInvoker::callMethodOnObject($extension, 'setupConsole');

		$builder = $extension->getContainerBuilder();

		$definition = $builder->getDefinition($builder->getByType('ApiGen\Console\Application'));
		$this->assertSame('ApiGen\Console\Application', $definition->getClass());

		$commandService = $definition->getSetup()[1]->arguments[0];
		$command = $builder->getDefinition($builder->getServiceName($commandService));
		$this->assertSame('ApiGen\Command\GenerateCommand', $command->getClass());
	}


	public function testIsPhar()
	{
		$extension = $this->getExtension();
		$this->assertFalse(MethodInvoker::callMethodOnObject($extension, 'isPhar'));
	}


	public function testSetupTemplatingFilters()
	{
		$extension = $this->getExtension();
		$extension->loadConfiguration();

		$builder = $extension->getContainerBuilder();
		$builder->prepareClassList();

		MethodInvoker::callMethodOnObject($extension, 'setupTemplatingFilters');

		$definition = $builder->getDefinition($builder->getByType('Latte\Engine'));
		$this->assertSame('Latte\Engine', $definition->getClass());

		$filterService = $definition->getSetup()[1]->arguments[1][0];
		$command = $builder->getDefinition($builder->getServiceName($filterService));
		$this->assertSame('ApiGen\Templating\Filters\AnnotationFilters', $command->getClass());
	}

	public function testSetupGeneratorQueue()
	{
		$extension = $this->getExtension();
		$extension->loadConfiguration();

		$builder = $extension->getContainerBuilder();
		$builder->prepareClassList();

		MethodInvoker::callMethodOnObject($extension, 'setupGeneratorQueue');

		$definition = $builder->getDefinition($builder->getByType('ApiGen\Generator\GeneratorQueue'));
		$this->assertSame('ApiGen\Generator\GeneratorQueue', $definition->getClass());

		$filterService = $definition->getSetup()[1]->arguments[0];
		$command = $builder->getDefinition($builder->getServiceName($filterService));
		$this->assertSame('ApiGen\Generator\TemplateGenerators\CombinedGenerator', $command->getClass());
	}


	/**
	 * @return ApiGenExtension
	 */
	private function getExtension()
	{
		$extension = new ApiGenExtension;
		$extension->setCompiler($this->getCompiler(), 'compiler');
		return $extension;
	}


	/**
	 * @return Compiler
	 */
	private function getCompiler()
	{
		$compiler = new Compiler(new ContainerBuilder);
		$compiler->compile(['parameters' => [
			'rootDir' => __DIR__,
			'tempDir' => __DIR__ . '/../temp'
		]], NULL, NULL);
		return $compiler;
	}

}
