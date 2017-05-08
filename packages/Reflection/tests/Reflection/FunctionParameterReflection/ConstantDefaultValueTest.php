<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\FunctionParameterReflection;

use ApiGen\Reflection\Reflection\FunctionParameterReflection;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ConstantDefaultValueTest extends AbstractParserAwareTestCase
{
    /**
     * @var FunctionParameterReflection
     */
    private $functionParameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $functionReflections = $this->reflectionStorage->getFunctionReflections();
        $functionReflection = $functionReflections[1];

        $functionParametersReflections = $functionReflection->getParameters();
        $this->functionParameterReflection = array_pop($functionParametersReflections);
    }

    public function testName(): void
    {
        $this->assertSame('hello', $this->functionParameterReflection->getName());
    }

    public function testType(): void
    {
        $this->assertSame('', $this->functionParameterReflection->getTypeHint());
        $this->assertNull($this->functionParameterReflection->getClassName());
    }

    public function testDefaultValue(): void
    {
        $this->assertSame('HI', $this->functionParameterReflection->getDefaultValueDefinition());
    }
}
