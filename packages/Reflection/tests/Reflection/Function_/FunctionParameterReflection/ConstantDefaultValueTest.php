<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection;

use ApiGen\Reflection\Reflection\Function_\FunctionParameterReflection;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ConstantDefaultValueTest extends AbstractParserAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection\Source';

    /**
     * @var FunctionParameterReflection
     */
    private $functionParameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $functionReflections = $this->reflectionStorage->getFunctionReflections();
        $functionReflection = $functionReflections[$this->namespacePrefix . '\functionWithConstant'];

        $this->functionParameterReflection = $functionReflection->getParameters()['hello'];
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
