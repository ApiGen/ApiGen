<?php

namespace ApiGen\Tests\Templating;

use ApiGen;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Tests\MethodInvoker;
use Mockery;
use PHPUnit_Framework_TestCase;


class TemplateFactoryTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var TemplateFactory
	 */
	private $templateFactory;


	protected function setUp()
	{
		$latteEngineMock = Mockery::mock('Latte\Engine');

		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOptions')->andReturn(['template' => ['templatesPath' => '...']]);

		$templateElementsLoaderMock = Mockery::mock('ApiGen\Templating\TemplateElementsLoader');
		$templateElementsLoaderMock->shouldReceive('addElementsToTemplate')->andReturnUsing(function ($args) {
			return $args;
		});

		$this->templateFactory = new TemplateFactory(
			$latteEngineMock, $configurationMock, $this->getTemplateNavigatorMock(), $templateElementsLoaderMock
		);
	}


	public function testCreate()
	{
		$this->assertInstanceOf('ApiGen\Templating\Template', $this->templateFactory->create());
	}


	public function testCreateForType()
	{
		$this->assertInstanceOf('ApiGen\Templating\Template', $this->templateFactory->createForType('overview'));
	}


	public function testCreateNamedForElement()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');

		$this->assertInstanceOf(
			'ApiGen\Templating\Template',
			$this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_SOURCE, $reflectionClassMock)
		);

		$this->assertInstanceOf(
			'ApiGen\Templating\Template',
			$this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_NAMESPACE, $reflectionClassMock)
		);

		$this->assertInstanceOf(
			'ApiGen\Templating\Template',
			$this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_PACKAGE, $reflectionClassMock)
		);
	}


	/**
	 * @expectedException ApiGen\Templating\Exceptions\UnsupportedElementException
	 */
	public function testCreateNamedForElementNonExisting()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$this->assertInstanceOf(
			'ApiGen\Templating\Template',
			$this->templateFactory->createNamedForElement('notExisting', $reflectionClassMock)
		);
	}


	public function testCreateForReflection()
	{
		$reflectionClassMock = Mockery::mock('ApiGen\Reflection\ReflectionClass');
		$template = $this->templateFactory->createForReflection($reflectionClassMock);
		$this->assertInstanceOf('ApiGen\Templating\Template', $template);

		$reflectionConstantMock = Mockery::mock('ApiGen\Reflection\ReflectionConstant');
		$template = $this->templateFactory->createForReflection($reflectionConstantMock);
		$this->assertInstanceOf('ApiGen\Templating\Template', $template);

		$reflectionFunctionMock = Mockery::mock('ApiGen\Reflection\ReflectionFunction');
		$template = $this->templateFactory->createForReflection($reflectionFunctionMock);
		$this->assertInstanceOf('ApiGen\Templating\Template', $template);
	}


	public function testBuildTemplateCache()
	{
		$template = MethodInvoker::callMethodOnObject($this->templateFactory, 'buildTemplate');
		$template2 = MethodInvoker::callMethodOnObject($this->templateFactory, 'buildTemplate');
		$this->assertSame($template, $template2);
	}


	/**
	 * @return Mockery\MockInterface
	 */
	private function getTemplateNavigatorMock()
	{
		$templateNavigatorMock = Mockery::mock('ApiGen\Templating\TemplateNavigator');
		$templateNavigatorMock->shouldReceive('getTemplatePath')->andReturnUsing(function ($arg) {
			return $arg . '-template-path.latte';
		});
		$templateNavigatorMock->shouldReceive('getTemplateFileName')->andReturn('...');
		$templateNavigatorMock->shouldReceive('getTemplatePathForClass')->andReturn();
		$templateNavigatorMock->shouldReceive('getTemplatePathForConstant')->andReturn();
		$templateNavigatorMock->shouldReceive('getTemplatePathForFunction')->andReturn();
		$templateNavigatorMock->shouldReceive('getTemplatePathForSourceElement')->andReturn();
		$templateNavigatorMock->shouldReceive('getTemplatePathForNamespace')->andReturn();
		$templateNavigatorMock->shouldReceive('getTemplatePathForPackage')->andReturn();
		return $templateNavigatorMock;
	}

}
