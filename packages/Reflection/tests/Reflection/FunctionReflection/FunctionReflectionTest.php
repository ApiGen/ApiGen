<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\FunctionReflection;

use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\FunctionParameterReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

final class FunctionReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Reflection\Tests\Reflection\FunctionReflection\Source';

    /**
     * @var FunctionReflectionInterface
     */
    private $functionReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $functionReflections = $this->reflectionStorage->getFunctionReflections();
        $this->functionReflection = array_pop($functionReflections);
    }

    public function testLines(): void
    {
        $this->assertSame(16, $this->functionReflection->getStartLine());
        $this->assertSame(19, $this->functionReflection->getEndLine());
    }

    public function testNames(): void
    {
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
    }

    public function testMisc(): void
    {
        $this->assertTrue($this->functionReflection->isDocumented());
    }
}
