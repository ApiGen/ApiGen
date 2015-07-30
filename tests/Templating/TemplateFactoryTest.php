<?php

namespace ApiGen\Tests\Templating;

use ApiGen;
use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\Exceptions\UnsupportedElementException;
use ApiGen\Templating\Template;
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

        $templateElementsLoaderMock = Mockery::mock(ApiGen\Templating\TemplateElementsLoader::class);
        $templateElementsLoaderMock->shouldReceive('addElementsToTemplate')->andReturnUsing(function ($args) {
            return $args;
        });

        $this->templateFactory = new TemplateFactory(
            $latteEngineMock,
            $configurationMock,
            $this->getTemplateNavigatorMock(),
            $templateElementsLoaderMock
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
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);

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


    public function testCreateNamedForElementNonExisting()
    {
        $this->setExpectedException(UnsupportedElementException::class);

        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $this->assertInstanceOf(
            'ApiGen\Templating\Template',
            $this->templateFactory->createNamedForElement('notExisting', $reflectionClassMock)
        );
    }


    public function testCreateForReflection()
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $template = $this->templateFactory->createForReflection($reflectionClassMock);
        $this->assertInstanceOf(Template::class, $template);

        $reflectionConstantMock = Mockery::mock(ConstantReflectionInterface::class);
        $template = $this->templateFactory->createForReflection($reflectionConstantMock);
        $this->assertInstanceOf(Template::class, $template);

        $reflectionFunctionMock = Mockery::mock(FunctionReflectionInterface::class);
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
