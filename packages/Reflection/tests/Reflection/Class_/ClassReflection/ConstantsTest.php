<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\AccessLevels;
use InvalidArgumentException;

final class ConstantsTest extends AbstractReflectionClassTestCase
{
    public function testName(): void
    {
        $this->assertSame(AccessLevels::class, $this->reflectionClass->getName());
    }

    public function testGetConstants(): void
    {
        $this->assertCount(2, $this->reflectionClass->getConstants());
    }

    public function testGetOwnConstants(): void
    {
        $this->assertCount(1, $this->reflectionClass->getOwnConstants());
    }

    public function testHasConstant(): void
    {
        $this->assertFalse($this->reflectionClass->hasConstant('NOT_EXISTING'));
        $this->assertTrue($this->reflectionClass->hasConstant('LEVEL'));
    }

    public function testGetConstant(): void
    {
        $this->assertInstanceOf(ClassConstantReflectionInterface::class, $this->reflectionClass->getConstant('LEVEL'));
    }

    public function testGetOwnConstant(): void
    {
        $this->assertInstanceOf(
            ClassConstantReflectionInterface::class,
            $this->reflectionClass->getOwnConstant('LEVEL')
        );
    }

    public function testGetOwnConstantNonExisting(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->reflectionClass->getOwnConstant('NON_EXISTING');
    }

    public function testGetConstantNonExisting(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->reflectionClass->getConstant('NON_EXISTING');
    }

    public function testGetInheritedConstants(): void
    {
        $this->assertCount(1, $this->reflectionClass->getInheritedConstants());
    }
}
