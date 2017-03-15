<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflections\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Parser\Tests\Reflection\ReflectionClass\AbstractReflectionClassTestCase;

class ParentsTest extends AbstractReflectionClassTestCase
{

    public function testGetParentClass()
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->reflectionClass->getParentClass());
    }


    public function testGetParentClassName()
    {
        $this->assertSame('Project\ParentClass', $this->reflectionClass->getParentClassName());
    }


    public function testGetParentClasses()
    {
        $this->assertCount(1, $this->reflectionClass->getParentClasses());
    }


    public function testGetParentClassNameList()
    {
        $this->assertSame(['Project\ParentClass'], $this->reflectionClass->getParentClassNameList());
    }


    public function testGetDirectSubClasses()
    {
        $this->assertCount(1, $this->reflectionClassOfParent->getDirectSubClasses());
    }


    public function testIndirectSubClasses()
    {
        $this->assertCount(0, $this->reflectionClassOfParent->getIndirectSubClasses());
    }
}
