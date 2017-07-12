<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Trait_\TraitMethodReflection;

use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Trait_\TraitMethodReflection\Source\TraitMethodTrait;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class TraitMethodReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var TraitMethodReflectionInterface
     */
    private $traitMethodReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $traitReflections = $this->reflectionStorage->getTraitReflections();
        $traitReflection = $traitReflections[TraitMethodTrait::class];

        $this->traitMethodReflection = $traitReflection->getMethod('methodWithArgs');
    }

    public function testGetDeclaringTrait(): void
    {
        $this->assertInstanceOf(TraitReflectionInterface::class, $this->traitMethodReflection->getDeclaringTrait());
        $this->assertSame(TraitMethodTrait::class, $this->traitMethodReflection->getDeclaringTraitName());
    }

    public function testGetParameters(): void
    {
        $parameters = $this->traitMethodReflection->getParameters();
        $this->assertCount(3, $parameters);
        $this->assertSame(['url', 'data', 'headers'], array_keys($parameters));
    }
}
