<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionBase;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\MethodInvoker;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

class ReflectionBaseTest extends TestCase
{

    /**
     * @var ReflectionBase
     */
    private $reflectionClass;


    protected function setUp()
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
    }


    public function testGetName()
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionClass->getName());
    }


    public function testGetPrettyName()
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionClass->getPrettyName());
    }


    public function testIsInternal()
    {
        $this->assertFalse($this->reflectionClass->isInternal());
    }


    public function testIsTokenized()
    {
        $this->assertTrue($this->reflectionClass->isTokenized());
    }


    public function testGetFileName()
    {
        $this->assertStringEndsWith('ReflectionMethod.php', $this->reflectionClass->getFileName());
    }


    public function testGetStartLine()
    {
        $this->assertSame(11, $this->reflectionClass->getStartLine());
    }


    public function testGetEndLine()
    {
        $this->assertSame(43, $this->reflectionClass->getEndLine());
    }


    public function testGetParsedClasses()
    {
        $parsedClasses = MethodInvoker::callMethodOnObject($this->reflectionClass, 'getParsedClasses');
        $this->assertCount(1, $parsedClasses);
    }


    /**
     * @return ReflectionFactoryInterface
     */
    private function getReflectionFactory()
    {
        $parserStorageMock = Mockery::mock(ParserStorageInterface::class, [
            'getElementsByType' => ['...']
        ]);

        $configurationMock = Mockery::mock(ConfigurationInterface::class, [
            'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC,
            'isInternalDocumented' => false,
        ]);
        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}
