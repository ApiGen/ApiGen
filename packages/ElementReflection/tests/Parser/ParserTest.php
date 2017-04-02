<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Tests\Parser;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\ElementReflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;

/**
 * Mirror to @see \ApiGen\Parser\Tests\ParserTest
 */
final class ParserTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Parser
     */
    private $parser;

    protected function setUp()
    {
        $this->parser = $this->container->getByType(Parser::class);
    }

    public function testFunctionReflection()
    {
        $this->parser->parseDirectories([__DIR__ . '/../../../../tests/Parser/Parser/ParserSource']);

        $functionReflections = $this->parser->getFunctionReflections();
        $this->assertCount(1, $functionReflections);

        $functionReflection = array_pop($functionReflections);
        $this->assertInstanceOf(FunctionReflectionInterface::class, $functionReflection);

        $this->assertSame(12, $functionReflection->getStartLine());
        $this->assertSame(14, $functionReflection->getEndLine());

        // namespaces
        $this->assertSame('SomeNamespace', $functionReflection->getNamespaceName());
        $this->assertSame('SomeNamespace', $functionReflection->getPseudoNamespaceName());

        // names
        $this->assertSame('SomeNamespace\someAloneFunction', $functionReflection->getName());
        $this->assertSame('someAloneFunction', $functionReflection->getShortName());
        $this->assertSame('SomeNamespace\someAloneFunction()', $functionReflection->getPrettyName());

        // annotations
        $this->assertCount(1, $functionReflection->getAnnotations());
        $this->assertTrue($functionReflection->hasAnnotation('return'));
        $this->assertFalse($functionReflection->hasAnnotation('param'));

        $returnAnnotation = $functionReflection->getAnnotation('return')[0];
        $this->assertInstanceOf(Return_::class, $returnAnnotation);
        $this->assertSame([], $functionReflection->getAnnotation('param'));

        $this->assertFalse($functionReflection->isDeprecated());

        $this->assertSame(
            'Some description.' . PHP_EOL . PHP_EOL . 'And more lines!',
            $functionReflection->getDescription()
        );

        // is documented
        $this->assertTrue($functionReflection->isDocumented());
    }
}
