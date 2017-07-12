<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ClassGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ClassGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ClassGenerator
     */
    private $classElementGenerator;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $this->classElementGenerator = $this->container->get(ClassGenerator::class);
    }

    public function testGenerate(): void
    {
        $this->classElementGenerator->generate();

        $this->assertFileExists(
            TEMP_DIR . '/class-ApiGen.Tests.Generator.Source.SomeClass.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-class-ApiGen.Tests.Generator.Source.SomeClass.html'
        );
    }
}
