<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\FunctionParameterReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Reflection\FunctionParameterReflection;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassTypeTest extends AbstractParserAwareTestCase
{
    /**
     * @var FunctionParameterReflection
     */
    private $functionParameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $functionReflections = $this->reflectionStorage->getFunctionReflections();
        $functionReflection = $functionReflections[2];

        $functionParametersReflections = $functionReflection->getParameters();
        $this->functionParameterReflection = array_pop($functionParametersReflections);
    }

    public function testName(): void
    {
        $this->assertSame('splFileInfo', $this->functionParameterReflection->getName());
    }

    public function testType(): void
    {
        $this->assertSame('SplFileInfo', $this->functionParameterReflection->getTypeHint());

        $typeHintClassReflection = $this->functionParameterReflection->getClass();
        $this->assertSame('SplFileInfo', $this->functionParameterReflection->getClassName());

        $this->assertInstanceOf(ClassReflectionInterface::class, $typeHintClassReflection);
    }
}
