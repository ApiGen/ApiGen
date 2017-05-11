<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitPropertyReflection;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Trait_\TraitPropertyReflection\Source\TraitProperty;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class TraitPropertyReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var TraitPropertyReflectionInterface
     */
    private $propertyReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $traitReflections = $this->reflectionStorage->getTraitReflections();
        $traitReflection = $traitReflections[TraitProperty::class];
        $this->propertyReflection = $traitReflection->getProperty('memberCount');
    }

    public function testGetDeclaringTrait(): void
    {
        $this->assertInstanceOf(TraitReflectionInterface::class, $this->propertyReflection->getDeclaringTrait());
        $this->assertSame(TraitProperty::class, $this->propertyReflection->getDeclaringTraitName());
    }
}
