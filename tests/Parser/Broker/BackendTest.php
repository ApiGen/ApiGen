<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Broker;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Broker\BackendInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TokenReflection\Broker;

final class BackendTest extends TestCase
{
    /**
     * @var BackendInterface
     */
    private $backend;

    /**
     * @var Broker
     */
    private $broker;

    protected function setUp(): void
    {
        $this->backend = new Backend($this->getReflectionFactory());
        $this->broker = new Broker($this->backend);
    }

    public function testGetClasses(): void
    {
        $this->broker->processDirectory(__DIR__ . '/BackendSource');
        $classes = $this->backend->getClasses();
        $this->assertCount(1, $classes);

        $class = array_pop($classes);
        $this->assertInstanceOf(ClassReflectionInterface::class, $class);

        $this->checkLoadedProperties($class);
    }

    public function testGetFunctions(): void
    {
        $this->broker->processDirectory(__DIR__ . '/BackendSource');
        $functions = $this->backend->getFunctions();
        $this->assertCount(1, $functions);

        $function = array_pop($functions);
        $this->assertInstanceOf(FunctionReflectionInterface::class, $function);

        $this->checkLoadedProperties($function);
    }

    public function testGetConstants(): void
    {
        $this->broker->processDirectory(__DIR__ . '/BackendSource');
        $constants = $this->backend->getConstants();
        $this->assertCount(1, $constants);

        $constant = array_pop($constants);
        $this->assertInstanceOf('ApiGen\Parser\Reflection\ReflectionConstant', $constant);

        $this->checkLoadedProperties($constant);
    }

    public function testPhp56DefaultArguments(): void
    {
        $this->broker->processDirectory(__DIR__ . '/BackendSourcePhp56DefaultArguments');
        $functions = $this->backend->getFunctions();
        $this->assertCount(2, $functions);
        foreach ($functions as $function) {
            $this->assertInstanceOf('ApiGen\Reflection\ReflectionFunction', $function);
            $this->checkLoadedProperties($function);
        }
        $constants = $this->backend->getConstants();
        $this->assertCount(1, $constants);
        foreach ($constants as $constant)
        {
            $this->assertInstanceOf('ApiGen\Reflection\ReflectionConstant', $constant);
            $this->checkLoadedProperties($constant);
        }
        $classes = $this->backend->getClasses();
        $this->assertCount(1, $classes);
        foreach ($classes as $class)
        {
            $this->assertInstanceOf('ApiGen\Reflection\ReflectionConstant', $constant);
            $this->checkLoadedProperties($constant);
        }
    }

    public function testPhp56Namespaces(): void
    {
        $this->broker->processDirectory(__DIR__ . '/BackendSourcePhp56Namespaces');
        $functions = $this->backend->getFunctions();
        $this->assertCount(3, $functions);
        foreach ($functions as $function) {
            $this->assertInstanceOf('ApiGen\Reflection\ReflectionFunction', $function);
            $this->checkLoadedProperties($function);
        }
        $constants = $this->backend->getConstants();
        $this->assertCount(2, $constants);
        foreach ($constants as $constant)
        {
            $this->assertInstanceOf('ApiGen\Reflection\ReflectionConstant', $constant);
            $this->checkLoadedProperties($constant);
        }
    }

    public function testPhp56VariadicFunctions(): void
    {
        $this->broker->processDirectory(__DIR__ . '/BackendSourcePhp56VariadicFunctions');
        $functions = $this->backend->getFunctions();
        $this->assertCount(2, $functions);
        $function = array_pop($functions);
        $this->assertInstanceOf('ApiGen\Reflection\ReflectionFunction', $function);
        $this->checkLoadedProperties($function);
    }

    /**
     * @param object $object
     */
    private function checkLoadedProperties($object): void
    {
        $this->assertInstanceOf(
            ConfigurationInterface::class,
            Assert::getObjectAttribute($object, 'configuration')
        );

        $this->assertInstanceOf(
            ParserStorageInterface::class,
            Assert::getObjectAttribute($object, 'parserStorage')
        );

        $this->assertInstanceOf(
            ReflectionFactoryInterface::class,
            Assert::getObjectAttribute($object, 'reflectionFactory')
        );
    }

    private function getReflectionFactory(): ReflectionFactory
    {
        $parserStoragetMock = $this->createMock(ParserStorageInterface::class);
        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('getVisibilityLevel')
            ->willReturn(1);

        return new ReflectionFactory($configurationMock, $parserStoragetMock);
    }
}
