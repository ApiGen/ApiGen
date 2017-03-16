<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Reflections\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Parser\Tests\Reflection\ReflectionClass\AbstractReflectionClassTestCase;
use TokenReflection;
use TokenReflection\Exception\RuntimeException;

class TraitsTest extends AbstractReflectionClassTestCase
{

    public function testIsTrait(): void
    {
        $this->assertFalse($this->reflectionClass->isTrait());
    }


    public function testGetTraits(): void
    {
        $traits = $this->reflectionClass->getTraits();
        $this->assertCount(2, $traits);
        $this->assertInstanceOf(ClassReflectionInterface::class, $traits['Project\SomeTrait']);
        $this->assertSame('Project\SomeTraitNotPresentHere', $traits['Project\SomeTraitNotPresentHere']);
    }


    public function testGetOwnTraits(): void
    {
        $traits = $this->reflectionClass->getOwnTraits();
        $this->assertCount(2, $traits);
    }


    public function testGetTraitNames(): void
    {
        $this->assertSame(
            ['Project\SomeTrait', 'Project\SomeTraitNotPresentHere'],
            $this->reflectionClass->getTraitNames()
        );
    }


    public function testGetOwnTraitName(): void
    {
        $this->assertSame(
            ['Project\SomeTrait', 'Project\SomeTraitNotPresentHere'],
            $this->reflectionClass->getOwnTraitNames()
        );
    }


    public function testGetTraitAliases(): void
    {
        $this->assertCount(0, $this->reflectionClass->getTraitAliases());
    }


    public function testGetTraitProperties(): void
    {
        $this->assertCount(1, $this->reflectionClass->getTraitProperties());
    }


    public function testGetTraitMethods(): void
    {
        $this->assertCount(1, $this->reflectionClass->getTraitMethods());
    }


    public function testUsesTrait(): void
    {
        $this->assertTrue($this->reflectionClass->usesTrait('Project\SomeTrait'));
        $this->assertFalse($this->reflectionClass->usesTrait('Project\NotActiveTrait'));
    }


    public function testUsesTraitNotExisting(): void
    {
        $this->assertTrue($this->reflectionClass->usesTrait('Project\SomeTraitNotPresentHere'));
    }


    public function testGetDirectUsers(): void
    {
        $this->assertCount(1, $this->reflectionClassOfTrait->getDirectUsers());
    }


    public function testGetIndirectUsers(): void
    {
        $this->assertCount(0, $this->reflectionClassOfTrait->getIndirectUsers());
    }
}
