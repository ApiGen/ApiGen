<?php

namespace ApiGen\Tests\Templating;

use ApiGen;
use ApiGen\Configuration\Configuration;
use ApiGen\Reflection\ReflectionClass;
use ApiGen\Reflection\ReflectionConstant;
use ApiGen\Reflection\ReflectionFunction;
use ApiGen\Templating\Template;
use ApiGen\Templating\TemplateElementsLoader;
use ApiGen\Templating\TemplateFactory;
use ApiGen\Templating\TemplateNavigator;
use ApiGen\Tests\MethodInvoker;
use Latte\Engine;
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
		$latteEngineMock = Mockery::mock(Engine::class);

		$configurationMock = Mockery::mock(Configuration::class);
		$configurationMock->shouldReceive('getOptions')->andReturn(['template' => ['templatesPath' => '...']]);

		$templateElementsLoaderMock = Mockery::mock(TemplateElementsLoader::class);
		$templateElementsLoaderMock->shouldReceive('addElementsToTemplate')->andReturnUsing(function ($args) {
			return $args;
		});

		$this->templateFactory = new TemplateFactory(
			$latteEngineMock, $configurationMock, $this->getTemplateNavigatorMock(), $templateElementsLoaderMock
		);
	}


	public function testCreate()
	{
		$this->assertInstanceOf(Template::class, $this->templateFactory->create());
	}


	public function testCreateForType()
	{
		$this->assertInstanceOf(Template::class, $this->templateFactory->createForType('overview'));
	}


	public function testCreateNamedForElement()
	{
		$reflectionClassMock = Mockery::mock(ReflectionClass::class);

		$this->assertInstanceOf(
			Template::class,
			$this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_SOURCE, $reflectionClassMock)
		);

		$this->assertInstanceOf(
			Template::class,
			$this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_NAMESPACE, $reflectionClassMock)
		);

		$this->assertInstanceOf(
			Template::class,
			$this->templateFactory->createNamedForElement(TemplateFactory::ELEMENT_PACKAGE, $reflectionClassMock)
		);
	}


	/**
	 * @expectedException ApiGen\Templating\Exceptions\UnsupportedElementException
	 */
	public function testCreateNamedForElementNonExisting()
	{
		$reflectionClassMock = Mockery::mock(ReflectionClass::class);
		$this->assertInstanceOf(
			Template::class,
			$this->templateFactory->createNamedForElement('notExisting', $reflectionClassMock)
		);
	}


	public function testCreateForReflection()
	{
		$reflectionClassMock = Mockery::mock(ReflectionClass::class);
		$template = $this->templateFactory->createForReflection($reflectionClassMock);
		$this->assertInstanceOf(Template::class, $template);

		$reflectionConstantMock = Mockery::mock(ReflectionConstant::class);
		$template = $this->templateFactory->createForReflection($reflectionConstantMock);
		$this->assertInstanceOf(Template::class, $template);

		$reflectionFunctionMock = Mockery::mock(ReflectionFunction::class);
		$template = $this->templateFactory->createForReflection($reflectionFunctionMock);
		$this->assertInstanceOf(Template::class, $template);
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
		$templateNavigatorMock = Mockery::mock(TemplateNavigator::class);
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
