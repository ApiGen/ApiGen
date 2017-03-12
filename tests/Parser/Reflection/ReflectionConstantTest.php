<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use ApiGen\Parser\Tests\Configuration\ParserConfiguration;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

class ReflectionConstantTest extends TestCase
{

    /**
     * @var ConstantReflectionInterface
     */
    private $constantReflection;

    /**
     * @var ConstantReflectionInterface
     */
    private $constantReflectionInClass;

    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;


    protected function setUp()
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionConstantSource');
        $this->constantReflection = $backend->getConstants()['SOME_CONSTANT'];

        /** @var ReflectionClass $reflectionClass */
        $this->reflectionClass = $backend->getClasses()['ConstantInClass'];
        $this->constantReflectionInClass = $this->reflectionClass->getConstant('CONSTANT_INSIDE');
    }


    public function testInstance()
    {
        $this->assertInstanceOf(ConstantReflectionInterface::class, $this->constantReflection);
        $this->assertInstanceOf(ConstantReflectionInterface::class, $this->constantReflectionInClass);
    }


    public function testGetDeclaringClass()
    {
        $this->assertNull($this->constantReflection->getDeclaringClass());
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->constantReflectionInClass->getDeclaringClass());
    }


    public function testGetDeclaringClassName()
    {
        $this->assertNull($this->constantReflection->getDeclaringClassName());
        $this->assertSame('ConstantInClass', $this->constantReflectionInClass->getDeclaringClassName());
    }


    public function testGetName()
    {
        $this->assertSame('SOME_CONSTANT', $this->constantReflection->getName());
        $this->assertSame('CONSTANT_INSIDE', $this->constantReflectionInClass->getName());
    }


    public function testGetShortName()
    {
        $this->assertSame('SOME_CONSTANT', $this->constantReflection->getShortName());
        $this->assertSame('CONSTANT_INSIDE', $this->constantReflectionInClass->getShortName());
    }


    public function testGetTypeHint()
    {
        $this->assertSame('string', $this->constantReflection->getTypeHint());
        $this->assertSame('int', $this->constantReflectionInClass->getTypeHint());
    }


    public function testGetValue()
    {
        $this->assertSame('some value', $this->constantReflection->getValue());
        $this->assertSame(55, $this->constantReflectionInClass->getValue());
    }


    public function testGetDefinition()
    {
        $this->assertSame("'some value'", $this->constantReflection->getValueDefinition());
        $this->assertSame('55', $this->constantReflectionInClass->getValueDefinition());
    }


    public function testIsValid()
    {
        $this->assertTrue($this->constantReflection->isValid());
        $this->assertTrue($this->constantReflectionInClass->isValid());
    }


    public function testIsDocumented()
    {
        $this->assertTrue($this->constantReflection->isDocumented());
        $this->assertTrue($this->constantReflectionInClass->isDocumented());
    }


    /**
     * @return Mockery\MockInterface
     */
    private function getReflectionFactory()
    {
        $parserResultMock = Mockery::mock(ParserStorageInterface::class);
        $parserResultMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
            if ($arg) {
                return ['ConstantInClass' => $this->reflectionClass];
            }
        });

        $configurationMock = Mockery::mock(ConfigurationInterface::class, [
            'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC,
            'isInternalDocumented' => false,
            'isPhpCoreDocumented' => true,
            'getMain' => ''
        ]);
        return new ReflectionFactory($configurationMock, $parserResultMock);
    }
}
