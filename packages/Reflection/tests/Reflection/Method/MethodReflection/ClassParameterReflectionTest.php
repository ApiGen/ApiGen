<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Method\MethodReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source\ParameterClass;
use ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source\ParameterMethodClass;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\Types\Object_;

final class ClassParameterReflectionTest extends AbstractParserAwareTestCase
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

        $methodReflection = $this->classReflection->getMethod('methodWithClassParameter');
        $this->parameterReflection = $methodReflection->getParameters()['parameterClass'];
    }

    public function testGetTypeHint(): void
    {
        $typeHints = $this->parameterReflection->getTypeHints();
        $this->assertCount(1, $typeHints);
        $this->assertInstanceOf(Object_::class, $typeHints[0]);
        $this->assertSame('\\' . ParameterClass::class, (string) $typeHints[0]->getFqsen());
    }
}
