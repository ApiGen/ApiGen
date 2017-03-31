<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Broker;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Broker\BackendInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\ElementReflection\Parser\Parser;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use PHPUnit\Framework\Assert;
use TokenReflection\Broker;

final class BackendTest extends AbstractContainerAwareTestCase
{
    private $sourceDirectory = __DIR__ . '/BackendSource';
    /**
     * @var BackendInterface
     */
    private $backend;

    /**
     * @var Broker
     */
    private $broker;

    /**
     * @var Parser
     */
    private $parser;

    protected function setUp(): void
    {
        $this->backend = $this->container->getByType(Backend::class);
        $this->broker = $this->container->getByType(Broker::class);
        $this->parser = $this->container->getByType(Parser::class);
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
        $this->broker->processDirectory($this->sourceDirectory);
        $functions = $this->backend->getFunctions();
        $this->assertCount(1, $functions);

        $function = array_pop($functions);
        $this->assertInstanceOf(FunctionReflectionInterface::class, $function);

        $this->checkLoadedProperties($function);
    }

    public function testGetFunctionsWithBetterReflection()
    {
        $this->parser->processDirectory($this->sourceDirectory);
        $functions = $this->parser->getFunctions();
        $this->assertCount(1, $functions);
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
}
