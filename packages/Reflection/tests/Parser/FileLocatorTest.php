<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class FileLocatorTest extends AbstractContainerAwareTestCase
{
    public function testSingle(): void
    {
        $parser = $this->container->get(Parser::class);
        $reflectionStorage = $this->container->get(ReflectionStorage::class);

        $parser->parseFilesAndDirectories([__DIR__ . '/NotLoadedSources/SomeClass.php']);

        $classReflections = $reflectionStorage->getClassReflections();
        $this->assertArrayHasKey(SomeClass::class, $classReflections);
    }
}
