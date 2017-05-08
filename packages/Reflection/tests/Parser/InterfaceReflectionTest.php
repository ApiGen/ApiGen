<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Reflection\InterfaceReflection;
use ApiGen\Tests\AbstractParserAwareTestCase;
use ApiGen\Tests\Parser\Parser\ParserSource\SomeInterface;

// @todo: mote to Reflection\InterfaceReflection test
final class InterfaceReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var InterfaceReflection
     */
    private $interfaceReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/../../../../tests/Parser/Parser/ParserSource']);

        $interfaceReflections = $this->reflectionStorage->getInterfaceReflections();
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
    }
}
