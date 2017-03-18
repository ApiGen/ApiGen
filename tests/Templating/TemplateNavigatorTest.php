<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use ApiGen\Templating\Filters\NamespaceUrlFilters;
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


    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(Configuration::class);
        $this->configuration->resolveOptions([
            'source' => __DIR__,
            'destination' => TEMP_DIR . '/api'
        ]);

        $sourceFiltersMock = $this->createMock(SourceFilters::class);
        $sourceFiltersMock->method('sourceUrl')->willReturnUsing(function ($args) {
            return 'source-code-' . $args->getName() . '.html';
        });
        $namespaceUrlFiltersMock = $this->createMock(NamespaceUrlFilters::class);
        $namespaceUrlFiltersMock->method('namespaceUrl')->willReturnUsing(function ($args) {
            return 'namespace-' . $args . '.html';
        });
        $namespaceUrlFiltersMock->method('packageUrl')->willReturnUsing(function ($args) {
            return 'package-' . $args . '.html';
        });

        $elementUrlFactoryMock = $this->createMock(ElementUrlFactory::class);
        $elementUrlFactoryMock->method('createForClass')->willReturnUsing(function ($args) {
            return 'class-' . $args->getName() . '.html';
        });
        $elementUrlFactoryMock->method('createForConstant')->willReturnUsing(function ($args) {
            return 'constant-' . $args->getName() . '.html';
        });
        $elementUrlFactoryMock->method('createForFunction')->willReturnUsing(function ($args) {
            return 'function-' . $args->getName() . '.html';
        });
        $this->templateNavigator = new TemplateNavigator(
            $this->configuration,
            $sourceFiltersMock,
            $elementUrlFactoryMock,
            $namespaceUrlFiltersMock
        );
    }


    public function testGetTemplateFileName(): void
    {
        $this->assertSame(
            TEMP_DIR . '/api/index.html',
            $this->templateNavigator->getTemplateFileName('overview')
        );
    }


    public function testGetTemplatePath(): void
    {
        $this->assertStringEndsWith(
            '/overview.latte',
            $this->templateNavigator->getTemplatePath('overview')
        );
    }


    public function testGetTemplatePathForNamespace(): void
    {
        $this->assertSame(
            TEMP_DIR . '/api/namespace-MyNamespace.html',
            $this->templateNavigator->getTemplatePathForNamespace('MyNamespace')
        );
    }


    public function testGetTemplatePathForClass(): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getName')->willReturn('SomeClass');

        $this->assertSame(
            TEMP_DIR . '/api/class-SomeClass.html',
            $this->templateNavigator->getTemplatePathForClass($classReflectionMock)
        );
    }


    public function testGetTemplatePathForConstant(): void
    {
        $constantReflectionMock = $this->createMock(ConstantReflectionInterface::class);
        $constantReflectionMock->method('getName')->willReturn('SomeConstant');

        $this->assertSame(
            TEMP_DIR . '/api/constant-SomeConstant.html',
            $this->templateNavigator->getTemplatePathForConstant($constantReflectionMock)
        );
    }


    public function testGetTemplatePathForFunction(): void
    {
        $functionReflectionMock = $this->createMock(FunctionReflectionInterface::class);
        $functionReflectionMock->method('getName')->willReturn('SomeFunction');

        $this->assertSame(
            TEMP_DIR . '/api/function-SomeFunction.html',
            $this->templateNavigator->getTemplatePathForFunction($functionReflectionMock)
        );
    }


    public function testGetTemplatePathForMethod(): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getName')->willReturn('SomeClass');

        $this->assertSame(
            TEMP_DIR . '/api/source-code-SomeClass.html',
            $this->templateNavigator->getTemplatePathForSourceElement($classReflectionMock)
        );
    }
}
