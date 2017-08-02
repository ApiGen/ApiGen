<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection;

use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ConstantDefaultValueTest extends AbstractParserAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection\Source';

    /**
     * @var FunctionParameterReflectionInterface
     */
    private $functionParameterReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $functionReflections = $this->reflectionStorage->getFunctionReflections();
        $functionReflection = $functionReflections[$this->namespacePrefix . '\functionWithConstant'];

        $this->functionParameterReflection = $functionReflection->getParameters()['hello'];
    }

    public function testType(): void
    {
        $this->assertSame('int', $this->functionParameterReflection->getTypeHint());
        // @todo - fix it after constant dump is fixed
        // $this->assertSame('HI', $this->functionParameterReflection->getDefaultValueDefinition());
    }
}
