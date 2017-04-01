<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Tests\Parser;

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

    public function test()
    {
        $this->parser->parseDirectories([__DIR__ . '/../../../../tests/Parser/Parser/ParserSource']);

        $functionReflections = $this->parser->getFunctionReflections();
        $this->assertCount(1, $functionReflections);
    }
}
