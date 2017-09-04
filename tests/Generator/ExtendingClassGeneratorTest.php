<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ClassGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ExtendingClassGeneratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassGenerator
     */
    private $classElementGenerator;

    protected function setUp(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/ExtendingSources']);

        $this->parser->parse();

        $this->classElementGenerator = $this->container->get(ClassGenerator::class);
    }

    public function testGenerate(): void
    {
        $this->classElementGenerator->generate();

        $this->assertFileExists(
            TEMP_DIR . '/class-ApiGen.Tests.Generator.ExtendingSources.ExtendingClass.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-class-ApiGen.Tests.Generator.ExtendingSources.ExtendingClass.html'
        );
    }
}
