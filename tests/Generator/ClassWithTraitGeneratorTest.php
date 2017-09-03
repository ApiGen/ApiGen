<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ClassGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassWithTraitGeneratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassGenerator
     */
    private $classElementGenerator;

    protected function setUp(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/UsingTraitsSources']);
        $this->parser->parse();

        $this->classElementGenerator = $this->container->get(ClassGenerator::class);
    }

    public function testGenerate(): void
    {
        $this->classElementGenerator->generate();

        $this->assertFileExists(
            TEMP_DIR . '/class-ApiGen.Tests.Generator.UsingTraitsSources.SomeClassWithTrait.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-class-ApiGen.Tests.Generator.UsingTraitsSources.SomeClassWithTrait.html'
        );
    }
}
