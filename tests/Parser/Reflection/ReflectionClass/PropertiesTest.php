<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;

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

    public function testGetInheritedProperties(): void
    {
        $this->assertCount(1, $this->reflectionClass->getInheritedProperties());
    }

    public function testGetUsedProperties(): void
    {
        $this->assertCount(1, $this->reflectionClass->getUsedProperties());
    }
}
