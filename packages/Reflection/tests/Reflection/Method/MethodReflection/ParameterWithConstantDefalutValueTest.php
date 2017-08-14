<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Method\MethodReflection;

use ApiGen\Reflection\Contract\Reflection\Method\MethodParameterReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Method\MethodReflection\Source\ParameterMethodClass;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\Types\String_;

final class ParameterWithConstantDefalutValueTest extends AbstractParserAwareTestCase
{
    /**
     * @var MethodParameterReflectionInterface
     */
    private $localConstantParameterReflection;

    /**
     * @var MethodParameterReflectionInterface
     */
    private $classConstantParameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $classReflection = $this->reflectionStorage->getClassReflections()[ParameterMethodClass::class];

        $methodReflection = $classReflection->getMethod('methodWithConstantDefaultValue');
        $this->localConstantParameterReflection = $methodReflection->getParameters()['where'];
        $this->classConstantParameterReflection = $methodReflection->getParameters()['when'];
    }

    public function testGetTypeHint(): void
    {
        $typeHints = $this->localConstantParameterReflection->getTypeHints();
        $this->assertCount(1, $typeHints);
        $this->assertInstanceOf(String_::class, $typeHints[0]);

        $typeHints = $this->classConstantParameterReflection->getTypeHints();
        $this->assertCount(1, $typeHints);
        $this->assertInstanceOf(String_::class, $typeHints[0]);
    }

    // @todo - fix after constant dump is fixed
    //public function testType(): void
    //{
    //    $this->assertSame('HERE', $this->localConstantParameterReflection->getDefaultValueDefinition());
    //    $this->assertSame('TODAY', $this->classConstantParameterReflection->getDefaultValueDefinition());
    //}
}
