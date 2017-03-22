<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflections\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Tests\Reflection\ReflectionClass\AbstractReflectionClassTestCase;

class PropertiesTest extends AbstractReflectionClassTestCase
{

    public function testGetProperty(): void
    {
        $this->assertInstanceOf(
            PropertyReflectionInterface::class,
            $this->reflectionClass->getProperty('publicProperty')
        );
    }


    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetPropertyNonExisting(): void
    {
        $this->reflectionClass->getProperty('notPresentProperty');
    }


    public function testGetProperties(): void
    {
        $this->assertCount(4, $this->reflectionClass->getProperties());
    }


    public function testGetOwnProperties(): void
    {
        $this->assertCount(2, $this->reflectionClass->getOwnProperties());
    }


    public function testGetMagicProperties(): void
    {
        $this->assertCount(3, $this->reflectionClass->getMagicProperties());
    }


    public function testGetOwnMagicProperties(): void
    {
        $this->assertCount(2, $this->reflectionClass->getOwnMagicProperties());
    }


    public function testGetInheritedProperties(): void
    {
        $this->assertCount(1, $this->reflectionClass->getInheritedProperties());
    }


    public function testGetInheritedMagicProperties(): void
    {
        $this->assertCount(0, $this->reflectionClass->getInheritedMagicProperties());
    }


    public function testGetUsedProperties(): void
    {
        $this->assertCount(1, $this->reflectionClass->getUsedProperties());
    }


    public function testGetUsedMagicProperties(): void
    {
        $this->assertCount(1, $this->reflectionClass->getUsedMagicProperties());
    }
}
