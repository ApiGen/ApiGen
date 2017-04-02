<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Tests\Parser;

use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\ElementReflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

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

        $this->assertSame('SomeNamespace', $functionReflection->getNamespaceName());
        $this->assertSame(5, $functionReflection->getStartLine());
        $this->assertSame(7, $functionReflection->getEndLine());

        $this->assertSame('SomeNamespace\someAloneFunction', $functionReflection->getName());
        $this->assertSame('someAloneFunction', $functionReflection->getShortName());
        $this->assertSame('SomeNamespace\someAloneFunction()', $functionReflection->getPrettyName());
    }
}
