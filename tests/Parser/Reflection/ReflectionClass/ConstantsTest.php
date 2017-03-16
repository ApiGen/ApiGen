<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflection\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use InvalidArgumentException;

class ConstantsTest extends AbstractReflectionClassTestCase
{

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
        $this->assertInstanceOf(ConstantReflectionInterface::class, $this->reflectionClass->getConstant('LEVEL'));
    }


    public function testHasOwnConstant(): void
    {
        $this->assertTrue($this->reflectionClass->hasOwnConstant('LEVEL'));
    }


    public function testGetOwnConstant(): void
    {
        $this->assertInstanceOf(
            'ApiGen\Parser\Reflection\ReflectionConstant',
            $this->reflectionClass->getOwnConstant('LEVEL')
        );
    }


    public function testGetOwnConstantNonExisting(): void
    {
        $this->reflectionClass->getOwnConstant('NON_EXISTING');
    }


    public function testGetConstantNonExisting(): void
    {
        $this->reflectionClass->getConstant('NON_EXISTING');
    }


    public function testGetInheritedConstants(): void
    {
        $this->assertCount(1, $this->reflectionClass->getInheritedConstants());
    }
}
