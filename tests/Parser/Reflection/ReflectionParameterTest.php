<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ParameterReflectionInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Project\ReflectionMethod;

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
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parserStorage = $parser->parseDirectories([__DIR__ . '/ReflectionMethodSource']);

        $this->reflectionClass = $parserStorage->getClasses()[ReflectionMethod::class];

        $reflectionMethod = $this->reflectionClass->getMethod('methodWithArgs');
        $reflectionParameters = $reflectionMethod->getParameters();
        $this->reflectionParameter = array_shift($reflectionParameters);
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(ParameterReflectionInterface::class, $this->reflectionParameter);
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame('int|string', $this->reflectionParameter->getTypeHint());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('the URL of the API endpoint', $this->reflectionParameter->getDescription());
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
            ClassReflectionInterface::class,
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
