<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ClassGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ClassWithTraitGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ClassGenerator
     */
    private $classElementGenerator;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseDirectories([__DIR__ . '/UsingTraitsSources']);

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
