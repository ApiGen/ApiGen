<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitPropertyReflection;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Trait_\TraitPropertyReflection\Source\TraitPropertyTrait;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class TraitPropertyReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var TraitPropertyReflectionInterface
     */
    private $propertyReflection;

    protected function setUp(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();
        $traitReflections = $this->reflectionStorage->getTraitReflections();
        $traitReflection = $traitReflections[TraitPropertyTrait::class];
        $this->propertyReflection = $traitReflection->getProperty('memberCount');
    }

    public function testGetDeclaringTrait(): void
    {
        $this->assertInstanceOf(TraitReflectionInterface::class, $this->propertyReflection->getDeclaringTrait());
        $this->assertSame(TraitPropertyTrait::class, $this->propertyReflection->getDeclaringTraitName());
    }
}
