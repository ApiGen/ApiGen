<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Configuration\Configuration;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\Tests\Parser\AnotherSource\ParentClassFromAnotherSource;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeClass;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeCountableClass;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ParserTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    /**
     * @var Parser
     */
    private $parser;

    protected function setUp(): void
    {
        $this->reflectionStorage = $this->container->get(ReflectionStorage::class);
        $this->configuration = $this->container->get(Configuration::class);
        $this->parser = $this->container->get(Parser::class);
    }


    public function testFilesAndDirectorySource(): void
    {
        $this->configuration->resolveOptions([
            'source' => [
                __DIR__ . '/NotLoadedSources/SomeClass.php',
                __DIR__ . '/AnotherSource',
            ],
            'destination' => TEMP_DIR
        ]);

        /** @var Parser $parser */
        $this->parser = $this->container->get(Parser::class);
        $parser->parse();

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
            'destination' => TEMP_DIR
        ]);

        $this->parser->parse();

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertArrayHasKey(SomeCountableClass::class, $classReflections);
    }
}
