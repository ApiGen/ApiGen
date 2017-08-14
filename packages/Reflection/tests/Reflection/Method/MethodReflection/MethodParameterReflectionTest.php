<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Method\MethodReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source\ParameterMethodClass;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;

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
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $this->classReflection = $this->reflectionStorage->getClassReflections()[ParameterMethodClass::class];

        $methodReflection = $this->classReflection->getMethod('methodWithArgs');
        $this->parameterReflection = $methodReflection->getParameters()['url'];
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(MethodParameterReflectionInterface::class, $this->parameterReflection);
    }

    public function testGetTypeHints(): void
    {
        $typeHints = $this->parameterReflection->getTypeHints();
        $this->assertInstanceOf(Integer::class, $typeHints[0]);
        $this->assertInstanceOf(String_::class, $typeHints[1]);
    }

    public function testGetIndexedTypeHints(): void
    {
        $methodReflection = $this->classReflection->getMethod('methodWithIndexedTypeHints');

        $parameter = $methodReflection->getParameters()['param1'];
        $typeHints = $parameter->getTypeHints();
        $this->assertInstanceOf(Integer::class, $typeHints[0]);
        $this->assertInstanceOf(String_::class, $typeHints[1]);

        $parameter = $methodReflection->getParameters()['param2'];
        $typeHints = $parameter->getTypeHints();
        $this->assertCount(1, $typeHints);
        $this->assertInstanceOf(Object_::class, $typeHints[0]);
        $this->assertSame('\ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\SomeClass', (string) $typeHints[0]->getFqsen());

        $parameter = $methodReflection->getParameters()['param3'];
        $typeHints = $parameter->getTypeHints();
        $this->assertCount(1, $typeHints);
        $this->assertInstanceOf(Object_::class, $typeHints[0]);
        $this->assertSame(
            '\ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source\ParameterMethodClass',
            (string) $typeHints[0]->getFqsen()
        );

        $parameter = $methodReflection->getParameters()['param4'];
        $typeHints = $parameter->getTypeHints();
        $this->assertCount(1, $typeHints);
        $this->assertInstanceOf(Object_::class, $typeHints[0]);
        $this->assertSame(
            '\stdClass',
            (string) $typeHints[0]->getFqsen()
        );
    }

    public function testGetDescription(): void
    {
        $this->assertSame('the URL of the API endpoint', $this->parameterReflection->getDescription());
    }

    public function testType(): void
    {
        $this->assertFalse($this->parameterReflection->isArray());
        $this->assertFalse($this->parameterReflection->isVariadic());
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

        $this->assertSame(
            ParameterMethodClass::class,
            $this->parameterReflection->getDeclaringClassName()
        );
    }

    public function testIsPassedByReference(): void
    {
        $this->assertFalse($this->parameterReflection->isPassedByReference());
    }
}
