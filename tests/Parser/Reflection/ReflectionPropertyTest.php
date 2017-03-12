<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use TokenReflection\Broker;

class ReflectionPropertyTest extends TestCase
{

    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    /**
     * @var PropertyReflectionInterface
     */
    private $reflectionProperty;


    protected function setUp()
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
        $this->reflectionProperty = $this->reflectionClass->getProperty('memberCount');
    }


    public function testInstance()
    {
        $this->assertInstanceOf(PropertyReflectionInterface::class, $this->reflectionProperty);
    }


    public function testIsReadOnly()
    {
        $this->assertFalse($this->reflectionProperty->isReadOnly());
    }


    public function testIsWriteOnly()
    {
        $this->assertFalse($this->reflectionProperty->isWriteOnly());
    }


    public function testIsMagic()
    {
        $this->assertFalse($this->reflectionProperty->isMagic());
    }


    public function testGetTypeHint()
    {
        $this->assertSame('integer', $this->reflectionProperty->getTypeHint());
    }


    public function testGetDeclaringClass()
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionProperty->getDeclaringClass());
    }


    public function testGetDeclaringClassName()
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionProperty->getDeclaringClassName());
    }


    public function testGetDefaultValue()
    {
        $this->assertSame(52, $this->reflectionProperty->getDefaultValue());
    }


    public function testIsDefault()
    {
        $this->assertTrue($this->reflectionProperty->isDefault());
    }


    public function testIsPrivate()
    {
        $this->assertFalse($this->reflectionProperty->isPrivate());
    }


    public function testIsProtected()
    {
        $this->assertFalse($this->reflectionProperty->isProtected());
    }


    public function testIsPublic()
    {
        $this->assertTrue($this->reflectionProperty->isPublic());
    }


    public function testIsStatic()
    {
        $this->assertFalse($this->reflectionProperty->isStatic());
    }


    public function testGetDeclaringTrait()
    {
        $this->assertNull($this->reflectionProperty->getDeclaringTrait());
    }


    public function testGetDeclaringTraitName()
    {
        $this->assertNull($this->reflectionProperty->getDeclaringTraitName());
    }


    public function testIsValid()
    {
        $this->assertTrue($this->reflectionProperty->isValid());
    }


    /**
     * @return ReflectionFactory
     */
    private function getReflectionFactory()
    {
        $parserResultMock = Mockery::mock(ParserStorageInterface::class);
        $parserResultMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
            if ($arg) {
                return ['Project\ReflectionMethod' => $this->reflectionClass];
            }
        });
        $configurationMock = Mockery::mock(ConfigurationInterface::class, [
            'getVisibilityLevel' => ReflectionProperty::IS_PUBLIC,
            'isInternalDocumented' => false,
            'isPhpCoreDocumented' => true
        ]);
        return new ReflectionFactory($configurationMock, $parserResultMock);
    }
}
