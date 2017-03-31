<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Tests\Parser;

use ApiGen\ElementReflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use BetterReflection\Reflection\ReflectionClass;

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
        $classReflections = $this->parser->parseDirectories([
            __DIR__ . '/../Parser'
        ]);
        $this->assertCount(1, $classReflections);

        /** @var ReflectionClass $classReflection */
        $classReflection = $classReflections[0];
        $this->assertInstanceOf(ReflectionClass::class, $classReflection);
    }
}
