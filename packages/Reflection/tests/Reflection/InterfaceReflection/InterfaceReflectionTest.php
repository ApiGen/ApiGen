<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\InterfaceReflection;

use ApiGen\Reflection\Contract\Reflection\InterfaceReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\FunctionReflection\Source\RichInterface;
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

        $interfaceReflections = $this->parser->getInterfaceReflections();
        $this->interfaceReflection = $interfaceReflections[1];
    }

    public function test()
    {
        $this->assertInstanceOf(InterfaceReflectionInterface::class, $this->interfaceReflection);
    }

    public function testImplementsInterface(): void
    {
        $this->assertFalse($this->interfaceReflection->extendsInterface('NoInterface'));
        $this->assertTrue($this->interfaceReflection->extendsInterface(RichInterface::class));
    }
//
//    public function testGetInterfaces(): void
//    {
//        $interfaces = $this->reflectionClass->getInterfaces();
//        $this->assertCount(1, $interfaces);
//        $this->assertInstanceOf(ClassReflectionInterface::class, $interfaces[RichInterface::class]);
//    }
//
//    public function testGetOwnInterfaces(): void
//    {
//        $interfaces = $this->reflectionClass->getOwnInterfaces();
//        $this->assertCount(1, $interfaces);
//        $this->assertInstanceOf(ClassReflectionInterface::class, $interfaces[RichInterface::class]);
//    }
//
//    public function testGetOwnInterfaceNames(): void
//    {
//        $this->assertSame([RichInterface::class], $this->reflectionClass->getOwnInterfaceNames());
//    }
//
//    public function testGetDirectImplementers(): void
//    {
//        $this->assertCount(1, $this->reflectionClassOfInterface->getDirectImplementers());
//    }
//
//    public function testGetIndirectImplementers(): void
//    {
//        $indirectImplementers = $this->reflectionClassOfInterface->getIndirectImplementers();
//        $this->assertSame([], $indirectImplementers);
//    }
}
