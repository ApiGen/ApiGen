<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\PoorInterface;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\RichInterface;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\SomeClass;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\SomeInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use Countable;

final class InterfaceReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var InterfaceReflectionInterface
     */
    private $interfaceReflection;

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $interfaceReflections = $this->reflectionStorage->getInterfaceReflections();
        $this->interfaceReflection = $interfaceReflections[SomeInterface::class];

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->classReflection = $classReflections[SomeClass::class];
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
        $this->assertArrayHasKey(RichInterface::class, $interfaces);
        $this->assertArrayHasKey(PoorInterface::class, $interfaces);
        $this->assertInstanceOf(InterfaceReflectionInterface::class, $interfaces[RichInterface::class]);
    }

    public function testLines(): void
    {
        $this->assertSame(5, $this->interfaceReflection->getStartLine());
        $this->assertSame(11, $this->interfaceReflection->getEndLine());
    }

    public function testFileName(): void
    {
        $this->assertSame(__DIR__ . '/Source/SomeInterface.php', $this->interfaceReflection->getFileName());

        $interfaces = $this->classReflection->getInterfaces();
        $this->assertNull($interfaces[Countable::class]->getFileName());
    }
}
