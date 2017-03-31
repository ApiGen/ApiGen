<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionFactoryInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionParameter;
use ApiGen\Parser\Reflection\TokenReflection\ReflectionFactory;
use PHPUnit\Framework\TestCase;
use Project\ReflectionMethod;
use ReflectionProperty;
use TokenReflection\Broker;

final class ReflectionParameterTest extends TestCase
{
    /**
     * @var ClassReflectionInterface
     */
    private $reflectionClass;

    /**
     * @var ParameterReflectionInterface
     */
    private $reflectionParameter;

    protected function setUp(): void
    {
        $backend = new Backend($this->getReflectionFactory());
        $broker = new Broker($backend);
        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()[ReflectionMethod::class];
        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
        $this->reflectionParameter = $reflectionMethod->getParameter(0);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(ReflectionParameter::class, $this->reflectionParameter);
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame('int|string', $this->reflectionParameter->getTypeHint());
    }

    public function testGetDescription(): void
    {
        $this->assertSame(' the URL of the API endpoint', $this->reflectionParameter->getDescription());
    }

    public function testGetPosition(): void
    {
        $this->assertSame(0, $this->reflectionParameter->getPosition());
    }

    public function testIsArray(): void
    {
        $this->assertFalse($this->reflectionParameter->isArray());
    }

    public function testGetClass(): void
    {
        $this->assertNull($this->reflectionParameter->getClass());
    }

    public function testGetClassName(): void
    {
        $this->assertNull($this->reflectionParameter->getClassName());
    }

    public function testGetDeclaringFunction(): void
    {
        $this->assertInstanceOf(MethodReflectionInterface::class, $this->reflectionParameter->getDeclaringFunction());
    }

    public function testGetDeclaringFunctionName(): void
    {
        $this->assertSame('methodWithArgs', $this->reflectionParameter->getDeclaringFunctionName());
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(
            'ApiGen\Parser\Reflection\ReflectionClass',
            $this->reflectionParameter->getDeclaringClass()
        );
    }

    public function testGetDeclaringClassName(): void
    {
        $this->assertSame('Project\ReflectionMethod', $this->reflectionParameter->getDeclaringClassName());
    }

    public function testIsUnlimited(): void
    {
        $this->assertFalse($this->reflectionParameter->isUnlimited());
    }

    private function getReflectionFactory(): ReflectionFactoryInterface
    {
        $parserStorageMock = $this->createMock(ParserStorageInterface::class);
        $parserStorageMock->method('getElementsByType')->willReturnCallback(function ($arg) {
            if ($arg) {
                return ['Project\ReflectionMethod' => $this->reflectionClass];
            }
        });
        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('getVisibilityLevel')
            ->willReturn(ReflectionProperty::IS_PUBLIC);

        return new ReflectionFactory($configurationMock, $parserStorageMock);
    }
}
