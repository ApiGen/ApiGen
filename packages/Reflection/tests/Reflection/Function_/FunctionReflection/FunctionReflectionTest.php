<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Function_\FunctionReflection;

use ApiGen\Reflection\Contract\Reflection\Function_\FunctionParameterReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

final class FunctionReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Reflection\Tests\Reflection\Function_\FunctionReflection\Source';

    /**
     * @var FunctionReflectionInterface
     */
    private $functionReflection;

    /**
     * @var FunctionReflectionInterface
     */
    private $simpleFunctionReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $functionReflections = $this->reflectionStorage->getFunctionReflections();
        $this->functionReflection = $functionReflections[$this->namespacePrefix . '\someAloneFunction'];
        $this->simpleFunctionReflection = $functionReflections[$this->namespacePrefix . '\add'];
    }

    public function testLines(): void
    {
        $this->assertSame(15, $this->functionReflection->getStartLine());
        $this->assertSame(18, $this->functionReflection->getEndLine());
    }

    public function testNames(): void
    {
        $this->assertSame('add', $this->simpleFunctionReflection->getShortName());

        $this->assertSame($this->namespacePrefix . '\someAloneFunction', $this->functionReflection->getName());
        $this->assertSame('someAloneFunction', $this->functionReflection->getShortName());
    }

    public function testNamespaces(): void
    {
        $this->assertSame(
            $this->namespacePrefix,
            $this->functionReflection->getNamespaceName()
        );
    }

    public function testAnnotations(): void
    {
        $this->assertCount(4, $this->functionReflection->getAnnotations());
        $this->assertTrue($this->functionReflection->hasAnnotation('return'));
        $this->assertTrue($this->functionReflection->hasAnnotation('param'));

        $returnAnnotation = $this->functionReflection->getAnnotation('return')[0];
        $this->assertInstanceOf(Return_::class, $returnAnnotation);
        $this->assertCount(3, $this->functionReflection->getAnnotation('param'));

        $this->assertFalse($this->functionReflection->isDeprecated());

        $this->assertSame(
            'Some description.' . PHP_EOL . PHP_EOL . 'And more lines!',
            $this->functionReflection->getDescription()
        );
    }

    public function testParameters(): void
    {
        $parameters = $this->functionReflection->getParameters();
        $this->assertCount(3, $parameters);

        foreach ($parameters as $parameter) {
            $this->assertInstanceOf(FunctionParameterReflectionInterface::class, $parameter);
        }

        $this->assertSame(['number', 'name', 'arguments'], array_keys($parameters));
    }

    public function testFileName(): void
    {
        $this->assertSame(__DIR__ . '/Source/SomeFunction.php', $this->functionReflection->getFileName());
    }
}
