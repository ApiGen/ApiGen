<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingClass;
use ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingInterface;
use ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingTrait;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeClass;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ParentResolvingTest extends AbstractParserAwareTestCase
{
    protected function setUp(): void
    {
        $this->configuration->resolveOptions([__DIR__ . '/ExtendingSources']);
        $this->parser->parse();
    }

    public function testClasses(): void
    {
        $allClasses = $this->reflectionStorage->getClassReflections();
        $this->assertCount(2, $allClasses);
        $this->assertArrayHasKey(SomeClass::class, $allClasses);
        $this->assertArrayHasKey(ExtendingClass::class, $allClasses);
    }

    public function testInterfaces(): void
    {
        $allInterfaces = $this->reflectionStorage->getInterfaceReflections();
        $this->assertArrayHasKey(SomeInterface::class, $allInterfaces);
        $this->assertArrayHasKey(ExtendingInterface::class, $allInterfaces);
        $this->assertCount(2, $allInterfaces);
    }

    public function testTraits(): void
    {
        $allTraits = $this->reflectionStorage->getTraitReflections();
        $this->assertArrayHasKey(ExtendingTrait::class, $allTraits);
        $this->assertCount(1, $allTraits);
    }
}
