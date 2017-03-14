<?php

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use TokenReflection\Broker;

class ReflectionMethodTest extends TestCase
{

    /**
     * @var MethodReflectionInterface
     */
    private $reflectionMethod;

    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;


    protected function setUp()
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()['Project\ReflectionMethod'];
        $this->reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
    }


    public function testGetDeclaringClass()
    {
        $this->isInstanceOf(ClassReflectionInterface::class, $this->reflectionMethod->getDeclaringClass());
    }


    public function testGetDeclaringClassName()
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionMethod->getDeclaringClassName());
    }


    public function testIsAbstract()
    {
        $this->assertFalse($this->reflectionMethod->isAbstract());
    }


    public function testIsFinal()
    {
        $this->assertFalse($this->reflectionMethod->isFinal());
    }


    public function testIsPrivate()
    {
        $this->assertFalse($this->reflectionMethod->isPrivate());
    }


    public function testIsProtected()
    {
        $this->assertFalse($this->reflectionMethod->isProtected());
    }


    public function testIsPublic()
    {
        $this->assertTrue($this->reflectionMethod->isPublic());
    }


    public function testIsStatic()
    {
        $this->assertFalse($this->reflectionMethod->isStatic());
    }


    public function testGetDeclaringTrait()
    {
        $this->assertNull($this->reflectionMethod->getDeclaringTrait());
    }


    public function testGetDeclaringTraitName()
    {
        $this->assertNull($this->reflectionMethod->getDeclaringTraitName());
    }


    public function testGetOriginalName()
    {
        $this->assertNull($this->reflectionMethod->getOriginalName());
    }


    public function testIsValid()
    {
        $this->assertTrue($this->reflectionMethod->isValid());
    }


    /** ReflectionFunctionBase methods */

    public function testGetParameters()
    {
        $this->assertCount(3, $this->reflectionMethod->getParameters());
    }


    /**
     * @return ReflectionFactoryInterface
     */
    private function getReflectionFactory()
    {
        $parserStorageMock = Mockery::mock(ParserStorageInterface::class);
        $parserStorageMock->shouldReceive('getElementsByType')->andReturnUsing(function ($arg) {
            if ($arg) {
                return ['Project\ReflectionMethod' => $this->reflectionClass];
            }
        });
        return new ReflectionFactory($this->getConfigurationMock(), $parserStorageMock);
    }


    /**
     * @return Mockery\MockInterface|ConfigurationInterface
     */
    private function getConfigurationMock()
    {
        $configurationMock = Mockery::mock(ConfigurationInterface::class, [
            'getVisibilityLevel' => 256,
            'isInternalDocumented' => false
        ]);
        return $configurationMock;
    }
}
