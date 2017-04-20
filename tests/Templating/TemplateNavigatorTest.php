<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Templating\TemplateNavigator;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Utils\FileSystem;

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

    /**
     * @var FileSystem
     */
    private $fileSystem;

    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(ConfigurationInterface::class);
        $this->configuration->resolveOptions([
            'source' => [__DIR__],
            'destination' => TEMP_DIR . '/api'
        ]);

        $this->templateNavigator = $this->container->getByType(TemplateNavigator::class);
        $this->fileSystem = new FileSystem();
    }

    public function testGetTemplatePathForClass(): void
    {
        $classReflectionMock = $this->createMock(ClassReflectionInterface::class);
        $classReflectionMock->method('getName')->willReturn('SomeClass');

        $this->assertSame(
            $this->fileSystem->normalizePath(
                TEMP_DIR . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'class-SomeClass.html'
            ),
            $this->templateNavigator->getTemplatePathForClass($classReflectionMock)
        );
    }

    public function testGetTemplatePathForFunction(): void
    {
        $functionReflectionMock = $this->createMock(FunctionReflectionInterface::class);
        $functionReflectionMock->method('getName')
            ->willReturn('SomeFunction');

        $this->assertSame(
            $this->fileSystem->normalizePath(
                TEMP_DIR . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'function-SomeFunction.html'
            ),
            $this->templateNavigator->getTemplatePathForFunction($functionReflectionMock)
        );
    }
}
