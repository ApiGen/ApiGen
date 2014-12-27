<?php

namespace ApiGen\Tests\Templating;

use ApiGen\Configuration\Configuration;
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
		$this->configuration = $this->container->getByType('ApiGen\Configuration\Configuration');
		$this->configuration->resolveOptions([
			'source' => __DIR__,
			'destination' => TEMP_DIR . '/api'
		]);

		$sourceFiltersMock = Mockery::mock('ApiGen\Templating\Filters\SourceFilters');
		$sourceFiltersMock->shouldReceive('sourceUrl')->andReturnUsing(function ($args) {
			return 'source-code-' . $args->getName() . '.html';
		});
		$namespaceAndPackageUrlFiltersMock = Mockery::mock('ApiGen\Templating\Filters\NamespaceAndPackageUrlFilters');
		$namespaceAndPackageUrlFiltersMock->shouldReceive('namespaceUrl')->andReturnUsing(function ($args) {
			return 'namespace-' . $args . '.html';
		});
		$namespaceAndPackageUrlFiltersMock->shouldReceive('packageUrl')->andReturnUsing(function ($args) {
			return 'package-' . $args . '.html';
		});

		$urlFiltersMock = Mockery::mock('ApiGen\Templating\Filters\UrlFilters');
		$urlFiltersMock->shouldReceive('classUrl')->andReturnUsing(function ($args) {
			return 'class-' . $args->getName() . '.html';
		});
		$urlFiltersMock->shouldReceive('constantUrl')->andReturnUsing(function ($args) {
			return 'constant-' . $args->getName() . '.html';
		});
		$urlFiltersMock->shouldReceive('functionUrl')->andReturnUsing(function ($args) {
			return 'function-' . $args->getName() . '.html';
		});
		$this->templateNavigator = new TemplateNavigator(
			$this->configuration, $sourceFiltersMock, $urlFiltersMock, $namespaceAndPackageUrlFiltersMock
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
			'/templates/default/overview.latte',
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
		$classReflectionMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$classReflectionMock->shouldReceive('getName')->andReturn('SomeClass');

		$this->assertSame(
			TEMP_DIR . '/api/class-SomeClass.html',
			$this->templateNavigator->getTemplatePathForClass($classReflectionMock)
		);
	}


	public function testGetTemplatePathForConstant()
	{
		$constantReflectionMock = Mockery::mock('ApiGen\Reflection\ReflectionConstant');
		$constantReflectionMock->shouldReceive('getName')->andReturn('SomeConstant');

		$this->assertSame(
			TEMP_DIR . '/api/constant-SomeConstant.html',
			$this->templateNavigator->getTemplatePathForConstant($constantReflectionMock)
		);
	}


	public function testGetTemplatePathForFunction()
	{
		$functionReflectionMock = Mockery::mock('ApiGen\Reflection\ReflectionFunction');
		$functionReflectionMock->shouldReceive('getName')->andReturn('SomeFunction');

		$this->assertSame(
			TEMP_DIR . '/api/function-SomeFunction.html',
			$this->templateNavigator->getTemplatePathForFunction($functionReflectionMock)
		);
	}


	public function testGetTemplatePathForMethod()
	{
		$classReflectionMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$classReflectionMock->shouldReceive('getName')->andReturn('SomeClass');

		$this->assertSame(
			TEMP_DIR . '/api/source-code-SomeClass.html',
			$this->templateNavigator->getTemplatePathForSourceElement($classReflectionMock)
		);
	}

}
