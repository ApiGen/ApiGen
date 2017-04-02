<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserStorageInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Parser\Broker\Backend;
use ApiGen\Parser\Reflection\ReflectionParameter;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Project\ReflectionMethod;
use TokenReflection\Broker;
use ApiGen\Parser\Reflection\ReflectionClass;

final class ReflectionParameterTest extends AbstractContainerAwareTestCase
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
        /** @var Backend $backend */
        $backend = $this->container->getByType(Backend::class);

        /** @var Broker $broker */
        $broker = $this->container->getByType(Broker::class);

        $broker->processDirectory(__DIR__ . '/ReflectionMethodSource');

        $this->reflectionClass = $backend->getClasses()[ReflectionMethod::class];

        /** @var ParserStorageInterface $parserStorage */
        $parserStorage = $this->container->getByType(ParserStorageInterface::class);
        $parserStorage->setClasses([
            ReflectionMethod::class => $this->reflectionClass
        ]);

        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
        $reflectionParameters = $reflectionMethod->getParameters();
        $this->reflectionParameter = array_shift($reflectionParameters);
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
            ReflectionClass::class,
            $this->reflectionParameter->getDeclaringClass()
        );
    }

    public function testGetDeclaringClassName(): void
    {
        $this->assertSame(ReflectionMethod::class, $this->reflectionParameter->getDeclaringClassName());
    }

    public function testIsVariadic(): void
    {
        $this->assertFalse($this->reflectionParameter->isVariadic());
    }
}
