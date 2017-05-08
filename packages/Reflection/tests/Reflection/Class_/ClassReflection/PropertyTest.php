<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\Class_\ClassReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Tests\Parser\Reflection\ReflectionClass\AbstractReflectionClassTestCase;

final class PropertyTest extends AbstractReflectionClassTestCase
{
    public function testGetProperty(): void
    {
        $this->assertInstanceOf(
            ClassPropertyReflectionInterface::class,
            $this->reflectionClass->getProperty('publicProperty')
        );
    }

//    /**
//     * @expectedException \InvalidArgumentException
//     */
//    public function testGetPropertyNonExisting(): void
//    {
//        $this->reflectionClass->getProperty('notPresentProperty');
//    }
//
//    public function testGetProperties(): void
//    {
//        $this->assertCount(4, $this->reflectionClass->getProperties());
//    }
//
//    public function testGetOwnProperties(): void
//    {
//        $this->assertCount(2, $this->reflectionClass->getOwnProperties());
//    }
//
//    public function testGetInheritedProperties(): void
//    {
//        $this->assertCount(1, $this->reflectionClass->getInheritedProperties());
//    }
//
//    public function testGetUsedProperties(): void
//    {
//        $this->assertCount(1, $this->reflectionClass->getUsedProperties());
//    }
}
