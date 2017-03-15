<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflections\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Parser\Tests\Reflection\ReflectionClass\AbstractReflectionClassTestCase;
use TokenReflection;
use TokenReflection\Exception\RuntimeException;

class TraitsTest extends AbstractReflectionClassTestCase
{

    public function testIsTrait()
    {
        $this->assertFalse($this->reflectionClass->isTrait());
    }


    public function testGetTraits()
    {
        $traits = $this->reflectionClass->getTraits();
        $this->assertCount(2, $traits);
        $this->assertInstanceOf(ClassReflectionInterface::class, $traits['Project\SomeTrait']);
        $this->assertSame('Project\SomeTraitNotPresentHere', $traits['Project\SomeTraitNotPresentHere']);
    }


    public function testGetOwnTraits()
    {
        $traits = $this->reflectionClass->getOwnTraits();
        $this->assertCount(2, $traits);
    }


    public function testGetTraitNames()
    {
        $this->assertSame(
            ['Project\SomeTrait', 'Project\SomeTraitNotPresentHere'],
            $this->reflectionClass->getTraitNames()
        );
    }


    public function testGetOwnTraitName()
    {
        $this->assertSame(
            ['Project\SomeTrait', 'Project\SomeTraitNotPresentHere'],
            $this->reflectionClass->getOwnTraitNames()
        );
    }


    public function testGetTraitAliases()
    {
        $this->assertCount(0, $this->reflectionClass->getTraitAliases());
    }


    public function testGetTraitProperties()
    {
        $this->assertCount(1, $this->reflectionClass->getTraitProperties());
    }


    public function testGetTraitMethods()
    {
        $this->assertCount(1, $this->reflectionClass->getTraitMethods());
    }


    public function testUsesTrait()
    {
        $this->assertTrue($this->reflectionClass->usesTrait('Project\SomeTrait'));
        $this->assertFalse($this->reflectionClass->usesTrait('Project\NotActiveTrait'));
    }


    /**
     * @expectedException \RuntimeException
     */
    public function testUsesTraitNotExisting()
    {
        $this->assertTrue($this->reflectionClass->usesTrait('Project\SomeTraitNotPresentHere'));
    }


    public function testGetDirectUsers()
    {
        $this->assertCount(1, $this->reflectionClassOfTrait->getDirectUsers());
    }


    public function testGetIndirectUsers()
    {
        $this->assertCount(0, $this->reflectionClassOfTrait->getIndirectUsers());
    }
}
