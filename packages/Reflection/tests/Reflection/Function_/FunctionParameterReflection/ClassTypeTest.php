<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassTypeTest extends AbstractParserAwareTestCase
{
    /**
     * @var FunctionParameterReflectionInterface
     */
    private $functionParameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $functionReflections = $this->reflectionStorage->getFunctionReflections();
        $functionReflection = $functionReflections[0];

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
