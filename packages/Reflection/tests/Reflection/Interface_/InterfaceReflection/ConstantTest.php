<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection;

use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Interface_\InterfaceReflection\Source\SomeInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ConstantTest extends AbstractParserAwareTestCase
{
    /**
     * @var InterfaceReflectionInterface
     */
    private $interfaceReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $interfaceReflections = $this->reflectionStorage->getInterfaceReflections();
        $this->interfaceReflection = $interfaceReflections[SomeInterface::class];
    }

    public function testGetConstants()
    {
        $ownConstants = $this->interfaceReflection->getOwnConstants();
        $this->assertCount(1, $ownConstants);
        $this->assertInstanceOf(InterfaceConstantReflectionInterface::class, $ownConstants[0]);

        $inheritedConstants = $this->interfaceReflection->getInheritedConstants();
        $this->assertCount(1, $inheritedConstants);
        $this->assertInstanceOf(InterfaceConstantReflectionInterface::class, $inheritedConstants[0]);

        $this->assertNotSame($ownConstants, $inheritedConstants);
    }

    public function testGestConstant()
    {
        $this->assertFalse($this->interfaceReflection->hasConstant('missing'));
        $this->assertTrue($this->interfaceReflection->hasConstant('LAST'));

        $this->assertInstanceOf(
            InterfaceConstantReflectionInterface::class,
            $this->interfaceReflection->getOwnConstant('LAST')
        );

        $this->assertInstanceOf(
            InterfaceConstantReflectionInterface::class,
            $this->interfaceReflection->getConstant('HOPE')
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testMissingConstant()
    {
        $this->interfaceReflection->getConstant('missing');
    }
}