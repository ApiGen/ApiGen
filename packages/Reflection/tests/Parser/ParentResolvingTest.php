<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ParentResolvingTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/ExtendingSources']);

        $this->reflectionStorage = $this->container->get(ReflectionStorage::class);
    }

    public function testClasses(): void
    {
        $allClasses = $this->reflectionStorage->getClassReflections();
        $this->assertCount(2, $allClasses);
        $this->assertArrayHasKey('ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeClass', $allClasses);
        $this->assertArrayHasKey('ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingClass', $allClasses);
    }

    public function testInterfaces(): void
    {
        $allInterfaces = $this->reflectionStorage->getInterfaceReflections();
        $this->assertArrayHasKey('ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeInterface', $allInterfaces);
        $this->assertArrayHasKey('ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingInterface', $allInterfaces);
        $this->assertCount(2, $allInterfaces);
    }

    public function testTraits(): void
    {
        $allTraits = $this->reflectionStorage->getTraitReflections();
        $this->assertArrayHasKey('ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingTrait', $allTraits);
        $this->assertCount(1, $allTraits);
    }
}
