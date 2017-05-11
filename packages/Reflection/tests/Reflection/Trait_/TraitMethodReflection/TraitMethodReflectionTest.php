<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitMethodReflection;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Trait_\TraitMethodReflection\Source\TraitMethod;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class TraitMethodReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var TraitMethodReflectionInterface
     */
    private $traitMethodReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $traitReflections = $this->reflectionStorage->getTraitReflections();
        $traitReflection = $traitReflections[TraitMethod::class];

        $this->traitMethodReflection = $traitReflection->getMethod('methodWithArgs');
    }

    public function testGetDeclaringTrait(): void
    {
        $this->assertInstanceOf(TraitReflectionInterface::class, $this->traitMethodReflection->getDeclaringTrait());
        $this->assertSame(TraitMethod::class, $this->traitMethodReflection->getDeclaringTraitName());
    }
}
