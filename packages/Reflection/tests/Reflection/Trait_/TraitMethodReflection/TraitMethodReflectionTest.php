<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitMethodReflection;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Trait_\TraitPropertyReflection\Source\TraitMethod;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class TraitMethodReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var TraitMethodReflectionInterface
     */
    private $propertyReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $traitReflections = $this->reflectionStorage->getTraitReflections();
        $traitReflection = $traitReflections[TraitMethod::class];
        $this->propertyReflection = $traitReflection->getMethod('memberCount');
    }

    public function testGetDeclaringTrait(): void
    {
        $this->assertInstanceOf(TraitReflectionInterface::class, $this->propertyReflection->getDeclaringTrait());
        $this->assertSame(TraitMethod::class, $this->propertyReflection->getDeclaringTraitName());
    }
}
