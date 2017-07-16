<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Method\MethodReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source\ParameterClass;
use ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source\ParameterMethodClass;
use ApiGen\Tests\AbstractParserAwareTestCase;

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
        $this->assertSame(ParameterClass::class, $this->parameterReflection->getTypeHint());
    }
}
