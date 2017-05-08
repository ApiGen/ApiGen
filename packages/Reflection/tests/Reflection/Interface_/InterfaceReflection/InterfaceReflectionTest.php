<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection;

use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\RichInterface;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\SomeInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class InterfaceReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var InterfaceReflectionInterface
     */
    private $interfaceReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $interfaceReflections = $this->reflectionStorage->getInterfaceReflections();
        $this->interfaceReflection = $interfaceReflections[1];
    }

    public function testNames(): void
    {
        $this->assertSame(SomeInterface::class, $this->interfaceReflection->getName());
        $this->assertSame('SomeInterface', $this->interfaceReflection->getShortName());
    }

    public function testImplementsInterface(): void
    {
        $this->assertFalse($this->interfaceReflection->implementsInterface('NoInterface'));
        $this->assertTrue($this->interfaceReflection->implementsInterface(RichInterface::class));
    }

    public function testGetInterfaces(): void
    {
        $interfaces = $this->interfaceReflection->getInterfaces();
        $this->assertCount(2, $interfaces);
        $this->assertInstanceOf(InterfaceReflectionInterface::class, $interfaces[0]);
    }

    public function testLines(): void
    {
        $this->assertSame(5, $this->interfaceReflection->getStartLine());
        $this->assertSame(8, $this->interfaceReflection->getEndLine());
    }
}
