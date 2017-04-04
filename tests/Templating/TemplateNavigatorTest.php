<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\TemplateNavigator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class TemplateNavigatorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var TemplateNavigator
     */
    private $templateNavigator;

    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(ConfigurationInterface::class);
        $this->configuration->resolveOptions([
            'source' => [__DIR__],
            'destination' => TEMP_DIR . '/api'
        ]);

        $this->templateNavigator = $this->container->getByType(TemplateNavigator::class);
    }

    public function testGetTemplateFileName(): void
    {
        $this->assertSame(
            TEMP_DIR . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'index.html',
            $this->templateNavigator->getTemplateFileName('overview'));
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
            TEMP_DIR . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'namespace-MyNamespace.html',
            $this->templateNavigator->getTemplatePathForNamespace('MyNamespace')
        );
    }

    public function testGetTemplatePathForClass(): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getName')->willReturn('SomeClass');

        $this->assertSame(
            TEMP_DIR . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'class-SomeClass.html',
            $this->templateNavigator->getTemplatePathForClass($classReflectionMock)
        );
    }

    public function testGetTemplatePathForConstant(): void
    {
        $constantReflectionMock = $this->createMock(ConstantReflectionInterface::class);
        $constantReflectionMock->method('getName')
            ->willReturn('SomeConstant');

        $this->assertSame(
            TEMP_DIR . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'constant-SomeConstant.html',
            $this->templateNavigator->getTemplatePathForConstant($constantReflectionMock)
        );
    }

    public function testGetTemplatePathForFunction(): void
    {
        $functionReflectionMock = $this->createMock(FunctionReflectionInterface::class);
        $functionReflectionMock->method('getName')
            ->willReturn('SomeFunction');

        $this->assertSame(
            TEMP_DIR . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'function-SomeFunction.html',
            $this->templateNavigator->getTemplatePathForFunction($functionReflectionMock)
        );
    }

    public function testGetTemplatePathForMethod(): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getName')
            ->willReturn('SomeClass');

        $this->assertSame(
            TEMP_DIR . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'source-class-SomeClass.html',
            $this->templateNavigator->getTemplatePathForSourceElement($classReflectionMock)
        );
    }
}
