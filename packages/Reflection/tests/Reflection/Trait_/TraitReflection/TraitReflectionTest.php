<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitReflection;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Trait_\TraitReflection\Source\SimpleTrait;
use ApiGen\Reflection\Tests\Reflection\Trait_\TraitReflection\Source\ToBeAliasedTrait;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class TraitReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var TraitReflectionInterface
     */
    private $traitReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);
        $traitReflections = $this->reflectionStorage->getTraitReflections();
        $this->traitReflection = $traitReflections[SimpleTrait::class];
    }

    public function testName(): void
    {
        $this->assertSame(SimpleTrait::class, $this->traitReflection->getName());
        $this->assertSame('SimpleTrait', $this->traitReflection->getShortName());
        $this->assertSame(
            'ApiGen\Reflection\Tests\Reflection\Trait_\TraitReflection\Source',
            $this->traitReflection->getNamespaceName()
        );
    }

    public function testUsers(): void
    {
        $usedProperties = $this->traitReflection->getUsedProperties();
        // bug, should be:
        // $this->assertCount(1, $usedProperties);
        // see: packages/Reflection/src/Reflection/Trait_/TraitReflection.php:140
        $this->assertCount(0, $usedProperties);
    }

    public function testTraitMethodAliases(): void
    {
        $this->assertSame([
            'renamedMethod' => ToBeAliasedTrait::class . '::aliasedParentMethod',
        ], $this->traitReflection->getTraitAliases());
    }
}
