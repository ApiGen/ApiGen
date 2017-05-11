<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Reflection\ReflectionClass;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\AbstractReflectionClassTestCase;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\AccessLevels;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\SomeTrait;

final class TraitsTest extends AbstractReflectionClassTestCase
{
    public function testName()
    {
        $this->assertSame(AccessLevels::class, $this->reflectionClass->getName());
    }

    public function testGetTraits(): void
    {
        $traits = $this->reflectionClass->getTraits();
        $this->assertCount(1, $traits);

        $this->assertInstanceOf(TraitReflectionInterface::class, $traits[SomeTrait::class]);
        // temporary disabled due to phpstan autoloading, might not be needed
        // $this->assertSame('Project\SomeTraitNotPresentHere', $traits['Project\SomeTraitNotPresentHere']);
    }

    public function testGetOwnTraits(): void
    {
        $this->assertCount(1, $this->reflectionClass->getOwnTraits());
    }

    public function testGetTraitAliases(): void
    {
        $this->assertCount(0, $this->reflectionClass->getTraitAliases());
    }

    public function testUsesTrait(): void
    {
        $this->assertTrue($this->reflectionClass->usesTrait(SomeTrait::class));
//        $this->assertFalse($this->reflectionClass->usesTrait('Project\NotActiveTrait'));
    }
//
//    /**
//     * @expectedException \TokenReflection\Exception\RuntimeException
//     */
//    public function testUsesTraitNotExisting(): void
//    {
//        $this->reflectionClass->usesTrait('Project\SomeTraitNotPresentHere');
//    }
//
//    public function testGetDirectUsers(): void
//    {
//        $this->assertCount(1, $this->reflectionClassOfTrait->getDirectUsers());
//    }
//
//    public function testGetIndirectUsers(): void
//    {
//        $this->assertCount(0, $this->reflectionClassOfTrait->getIndirectUsers());
//    }
}
