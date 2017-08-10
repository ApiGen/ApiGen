<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\Tests\Parser\AnotherSource\ParentClassFromAnotherSource;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeClass;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeCountableClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ParserTest extends AbstractContainerAwareTestCase
{
    public function testFilesAndDirectorySource(): void
    {
        $parser = $this->container->get(Parser::class);
        $reflectionStorage = $this->container->get(ReflectionStorage::class);

        $parser->parseFilesAndDirectories([
            __DIR__ . '/NotLoadedSources/SomeClass.php',
            __DIR__ . '/AnotherSource',
        ]);

        $classReflections = $reflectionStorage->getClassReflections();
        $this->assertArrayHasKey(SomeClass::class, $classReflections);
        $this->assertArrayHasKey(ParentClassFromAnotherSource::class, $classReflections);
    }

    public function testFiles(): void
    {
        $parser = $this->container->get(Parser::class);
        $reflectionStorage = $this->container->get(ReflectionStorage::class);

        $parser->parseFilesAndDirectories([
            __DIR__ . '/NotLoadedSources/SomeCountableClass.php',
        ]);

        $classReflections = $reflectionStorage->getClassReflections();
        $this->assertArrayHasKey(SomeCountableClass::class, $classReflections);
    }
}
