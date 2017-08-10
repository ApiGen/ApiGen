<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ParentResolvingTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/ExtendingSources']);

        $reflectionStorage = $this->container->get(ReflectionStorage::class);

        $allClasses = $reflectionStorage->getClassReflections();

        self::assertCount(2, $allClasses);
        self::assertArrayHasKey('ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeClass', $allClasses);
        self::assertArrayHasKey('ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingClass', $allClasses);

        $allInterfaces = $reflectionStorage->getInterfaceReflections();
        self::assertArrayHasKey('ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeInterface', $allInterfaces);
        self::assertArrayHasKey('ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingInterface', $allInterfaces);
        self::assertCount(2, $allInterfaces);

        $allTraits = $reflectionStorage->getTraitReflections();
        self::assertArrayHasKey('ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeTrait', $allTraits);
        self::assertArrayHasKey('ApiGen\Reflection\Tests\Parser\ExtendingSources\ExtendingTrait', $allTraits);
        self::assertCount(2, $allTraits);
    }
}
