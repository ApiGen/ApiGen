<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection;

use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\Types\Integer;

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
        $typeHints = $this->functionParameterReflection->getTypeHints();
        $this->assertCount(1, $typeHints);
        $this->assertInstanceOf(Integer::class, $typeHints[0]);

        // @todo - fix it after constant dump is fixed
        // $this->assertSame('HI', $this->functionParameterReflection->getDefaultValueDefinition());
    }
}
