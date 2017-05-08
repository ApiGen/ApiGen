<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\FunctionParameterReflection;

use ApiGen\Reflection\Contract\Reflection\FunctionReflectionInterface;
use ApiGen\Reflection\Reflection\FunctionParameterReflection;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class FunctionParameterReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Reflection\Tests\Reflection\FunctionParameterReflection\Source';

    /**
     * @var FunctionParameterReflection
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
        $this->assertSame('arguments', $this->functionParameterReflection->getName());
    }

    public function testDeclaringFunction(): void
    {
        $this->assertInstanceOf(
            FunctionReflectionInterface::class,
            $this->functionParameterReflection->getDeclaringFunction()
        );

        $this->assertSame(
            $this->namespacePrefix . '\someAloneFunction',
            $this->functionParameterReflection->getDeclaringFunctionName()
        );
    }

    public function testType(): void
    {
        $this->assertSame('string', $this->functionParameterReflection->getTypeHint());
        $this->assertTrue($this->functionParameterReflection->isVariadic());
        $this->assertFalse($this->functionParameterReflection->isArray());
        $this->assertFalse($this->functionParameterReflection->isCallable());

        // @todo: add test for class
        $this->assertNull($this->functionParameterReflection->getClass());
        $this->assertNull($this->functionParameterReflection->getClassName());
    }


    public function testDescription(): void
    {
        $this->assertSame('and their description', $this->functionParameterReflection->getDescription());
    }

    public function testDefaults(): void
    {
        $this->assertNull($this->functionParameterReflection->getDefaultValueDefinition());
    }
}
