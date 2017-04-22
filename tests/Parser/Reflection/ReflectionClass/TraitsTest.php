<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\ReflectionClass;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Tests\Parser\Reflection\ReflectionClassSource\SomeTrait;

final class TraitsTest extends AbstractReflectionClassTestCase
{
    public function testIsTrait(): void
    {
        $this->assertFalse($this->reflectionClass->isTrait());
    }

    public function testGetTraits(): void
    {
        $traits = $this->reflectionClass->getTraits();
        $this->assertCount(1, $traits);
        $this->assertInstanceOf(ClassReflectionInterface::class, $traits[SomeTrait::class]);
        // temporary disabled due to phpstan autoloading, might not be needed
        // $this->assertSame('Project\SomeTraitNotPresentHere', $traits['Project\SomeTraitNotPresentHere']);
    }

    public function testGetOwnTraits(): void
    {
        $traits = $this->reflectionClass->getOwnTraits();
        $this->assertCount(1, $traits);
    }

    public function testGetOwnTraitName(): void
    {
        $this->assertSame([SomeTrait::class], $this->reflectionClass->getOwnTraitNames());
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
        $this->assertTrue($this->reflectionClass->usesTrait(SomeTrait::class));
        $this->assertFalse($this->reflectionClass->usesTrait('Project\NotActiveTrait'));
    }

    /**
     * @expectedException \TokenReflection\Exception\RuntimeException
     */
    public function testUsesTraitNotExisting(): void
    {
        $this->reflectionClass->usesTrait('Project\SomeTraitNotPresentHere');
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
