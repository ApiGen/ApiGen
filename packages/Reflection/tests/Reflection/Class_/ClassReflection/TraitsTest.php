<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\AccessLevels;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\SomeTrait;

final class TraitsTest extends AbstractReflectionClassTestCase
{
    public function testName(): void
    {
        $this->assertSame(AccessLevels::class, $this->reflectionClass->getName());
    }

    public function testGetTraits(): void
    {
        $traits = $this->reflectionClass->getTraits();
        $this->assertCount(1, $traits);

        $this->assertInstanceOf(TraitReflectionInterface::class, $traits[SomeTrait::class]);
    }

    public function testGetTraitAliases(): void
    {
        $this->assertCount(0, $this->reflectionClass->getTraitAliases());
    }
}
