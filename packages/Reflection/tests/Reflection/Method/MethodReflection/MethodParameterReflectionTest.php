<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Method_\MethodReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Method_\MethodReflection\Source\ParameterMethodClass;
use ApiGen\Tests\AbstractParserAwareTestCase;
use ApiGen\Tests\Parser\Reflection\ReflectionMethodSource\ReflectionMethod;

final class MethodParameterReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    /**
     * @var MethodParameterReflectionInterface
     */
    private $parameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $this->classReflection = $this->reflectionStorage->getClassReflections()[ParameterMethodClass::class];

        $methodReflection = $this->classReflection->getMethod('methodWithArgs');
        $this->parameterReflection = $methodReflection->getParameters()['url'];
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(MethodParameterReflectionInterface::class, $this->parameterReflection);
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame('int|string', $this->parameterReflection->getTypeHint());
    }

    public function testGetDescription(): void
    {
        $this->assertSame('the URL of the API endpoint', $this->parameterReflection->getDescription());
    }

    public function testIsArray(): void
    {
        $this->assertFalse($this->parameterReflection->isArray());
    }

    public function testGetClass(): void
    {
        $this->assertNull($this->parameterReflection->getClass());
    }

    public function testGetClassName(): void
    {
        $this->assertNull($this->parameterReflection->getClassName());
    }

    public function testGetDeclaringFunction(): void
    {
        $this->assertInstanceOf(
            ClassMethodReflectionInterface::class,
            $this->parameterReflection->getDeclaringMethod()
        );
    }

    public function testGetDeclaringFunctionName(): void
    {
        $this->assertSame(
            'methodWithArgs',
            $this->parameterReflection->getDeclaringMethodName()
        );
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(
            ClassReflectionInterface::class,
            $this->parameterReflection->getDeclaringClass()
        );
    }

    public function testGetDeclaringClassName(): void
    {
        $this->assertSame(
            ParameterMethodClass::class,
            $this->parameterReflection->getDeclaringClassName()
        );
    }

    public function testIsVariadic(): void
    {
        $this->assertFalse($this->parameterReflection->isVariadic());
    }
}
