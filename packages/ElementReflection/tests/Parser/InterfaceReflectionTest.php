<?php declare(strict_types=1);

namespace ApiGen\ElementReflection\Tests\Parser;

use ApiGen\ElementReflection\Parser\Parser;
use ApiGen\ElementReflection\Reflection\InterfaceReflection;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\Parser\Parser\ParserSource\SomeInterface;

/**
 * Mirror to Function test @see \ApiGen\Tests\ParserTest
 */
final class InterfaceReflectionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var InterfaceReflection
     */
    private $interfaceReflection;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->getByType(Parser::class);
        $parser->parseDirectories([__DIR__ . '/../../../../tests/Parser/Parser/ParserSource']);

        $interfaceReflections = $parser->getInterfaceReflections();
        $this->interfaceReflection = array_shift($interfaceReflections);
    }

    public function testLines(): void
    {
        $this->assertSame(5, $this->interfaceReflection->getStartLine());
        $this->assertSame(11, $this->interfaceReflection->getEndLine());
    }

    public function testNames(): void
    {
        $this->assertSame(SomeInterface::class, $this->interfaceReflection->getName());
        $this->assertSame('SomeInterface', $this->interfaceReflection->getShortName());
        $this->assertSame(SomeInterface::class. '()', $this->interfaceReflection->getPrettyName());
    }
}
