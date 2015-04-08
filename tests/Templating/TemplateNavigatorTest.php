<?php

namespace ApiGen\Tests\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use ApiGen\Templating\Filters\NamespaceAndPackageUrlFilters;
use ApiGen\Templating\Filters\SourceFilters;
use ApiGen\Templating\TemplateNavigator;
use ApiGen\Tests\ContainerAwareTestCase;
use Mockery;


class TemplateNavigatorTest extends ContainerAwareTestCase
{

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var TemplateNavigator
	 */
	private $templateNavigator;


	protected function setUp()
	{
		$this->configuration = $this->container->getByType(Configuration::class);
		$this->configuration->resolveOptions([
			'source' => __DIR__,
			'destination' => TEMP_DIR . '/api'
		]);

		$sourceFiltersMock = Mockery::mock(SourceFilters::class);
		$sourceFiltersMock->shouldReceive('sourceUrl')->andReturnUsing(function ($args) {
			return 'source-code-' . $args->getName() . '.html';
		});
		$namespaceAndPackageUrlFiltersMock = Mockery::mock(NamespaceAndPackageUrlFilters::class);
		$namespaceAndPackageUrlFiltersMock->shouldReceive('namespaceUrl')->andReturnUsing(function ($args) {
			return 'namespace-' . $args . '.html';
		});
		$namespaceAndPackageUrlFiltersMock->shouldReceive('packageUrl')->andReturnUsing(function ($args) {
			return 'package-' . $args . '.html';
		});

		$elementUrlFactoryMock = Mockery::mock(ElementUrlFactory::class);
		$elementUrlFactoryMock->shouldReceive('createForClass')->andReturnUsing(function ($args) {
			return 'class-' . $args->getName() . '.html';
		});
		$elementUrlFactoryMock->shouldReceive('createForConstant')->andReturnUsing(function ($args) {
			return 'constant-' . $args->getName() . '.html';
		});
		$elementUrlFactoryMock->shouldReceive('createForFunction')->andReturnUsing(function ($args) {
			return 'function-' . $args->getName() . '.html';
		});
		$this->templateNavigator = new TemplateNavigator(
			$this->configuration, $sourceFiltersMock, $elementUrlFactoryMock, $namespaceAndPackageUrlFiltersMock
		);
	}


	public function testGetTemplateFileName()
	{
		$this->assertSame(
			TEMP_DIR . '/api/index.html',
			$this->templateNavigator->getTemplateFileName('overview')
		);
	}


	public function testGetTemplatePath()
	{
		$this->assertStringEndsWith(
			'/overview.latte',
			$this->templateNavigator->getTemplatePath('overview')
		);
	}


	public function testGetTemplatePathForNamespace()
	{
		$this->assertSame(
			TEMP_DIR . '/api/namespace-MyNamespace.html',
			$this->templateNavigator->getTemplatePathForNamespace('MyNamespace')
		);
	}


	public function testGetTemplatePathForPackage()
	{
		$this->assertSame(
			TEMP_DIR . '/api/package-MyPackage.html',
			$this->templateNavigator->getTemplatePathForPackage('MyPackage')
		);
	}


	public function testGetTemplatePathForClass()
	{
		$classReflectionMock = Mockery::mock(ClassReflectionInterface::class);
		$classReflectionMock->shouldReceive('getName')->andReturn('SomeClass');

		$this->assertSame(
			TEMP_DIR . '/api/class-SomeClass.html',
			$this->templateNavigator->getTemplatePathForClass($classReflectionMock)
		);
	}


	public function testGetTemplatePathForConstant()
	{
		$constantReflectionMock = Mockery::mock(ConstantReflectionInterface::class);
		$constantReflectionMock->shouldReceive('getName')->andReturn('SomeConstant');

		$this->assertSame(
			TEMP_DIR . '/api/constant-SomeConstant.html',
			$this->templateNavigator->getTemplatePathForConstant($constantReflectionMock)
		);
	}


	public function testGetTemplatePathForFunction()
	{
		$functionReflectionMock = Mockery::mock(FunctionReflectionInterface::class);
		$functionReflectionMock->shouldReceive('getName')->andReturn('SomeFunction');

		$this->assertSame(
			TEMP_DIR . '/api/function-SomeFunction.html',
			$this->templateNavigator->getTemplatePathForFunction($functionReflectionMock)
		);
	}


	public function testGetTemplatePathForMethod()
	{
		$classReflectionMock = Mockery::mock(ClassReflectionInterface::class);
		$classReflectionMock->shouldReceive('getName')->andReturn('SomeClass');

		$this->assertSame(
			TEMP_DIR . '/api/source-code-SomeClass.html',
			$this->templateNavigator->getTemplatePathForSourceElement($classReflectionMock)
		);
	}

}
