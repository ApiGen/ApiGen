<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\PoorInterface;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\RichInterface;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\SomeClass;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\SomeInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ImplementersTest extends AbstractParserAwareTestCase
{
    /**
     * @var InterfaceReflectionInterface
     */
    private $interfaceReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $interfaceReflections = $this->reflectionStorage->getInterfaceReflections();
        $this->interfaceReflection = $interfaceReflections[PoorInterface::class];
    }

    public function testGetInterfaces(): void
    {
        $this->assertCount(0, $this->interfaceReflection->getInterfaces());
    }

    public function testGetImplementers(): void
    {
        $implementers = $this->interfaceReflection->getImplementers();

        $this->assertCount(3, $implementers);

        $this->assertArrayHasKey(SomeClass::class, $implementers);
        $this->assertInstanceOf(ClassReflectionInterface::class, $implementers[SomeClass::class]);

        $this->assertArrayHasKey(RichInterface::class, $implementers);
        $this->assertInstanceOf(InterfaceReflectionInterface::class, $implementers[RichInterface::class]);

        $this->assertArrayHasKey(SomeInterface::class, $implementers);
        $this->assertInstanceOf(InterfaceReflectionInterface::class, $implementers[SomeInterface::class]);
    }
}
