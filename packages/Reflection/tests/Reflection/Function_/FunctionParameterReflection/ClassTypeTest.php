<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionParameterReflection;

use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\Types\Object_;

final class ClassTypeTest extends AbstractParserAwareTestCase
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
        $functionReflection = $functionReflections[$this->namespacePrefix . '\functionWithClass'];

        $this->functionParameterReflection = $functionReflection->getParameters()['splFileInfo'];
    }

    public function testType(): void
    {
        $typeHints = $this->functionParameterReflection->getTypeHints();
        $this->assertCount(1, $typeHints);
        $this->assertInstanceOf(Object_::class, $typeHints[0]);
        $this->assertSame('\SplFileInfo', (string) $typeHints[0]->getFqsen());
    }
}
