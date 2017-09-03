<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Tests\Parser\AnotherSource\ParentClassFromAnotherSource;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeClass;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeCountableClass;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ParserTest extends AbstractParserAwareTestCase
{
    public function testFilesAndDirectorySource(): void
    {
        $this->resolveConfigurationBySource([
            __DIR__ . '/NotLoadedSources/SomeClass.php',
            __DIR__ . '/AnotherSource',
        ]);
        $this->parser->parse();

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertArrayHasKey(SomeClass::class, $classReflections);
        $this->assertArrayHasKey(ParentClassFromAnotherSource::class, $classReflections);
    }

    public function testFiles(): void
    {
        $this->configuration->resolveOptions([
            'source' => [
                __DIR__ . '/NotLoadedSources/SomeCountableClass.php',
            ],
            'destination' => TEMP_DIR,
        ]);

        $this->parser->parse();

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertArrayHasKey(SomeCountableClass::class, $classReflections);
    }
}
