<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\TraitGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class TraitGeneratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var TraitGenerator
     */
    private $traitGenerator;

    protected function setUp(): void
    {
        $this->configuration->resolveOptions([
            'source' => __DIR__ . '/Source',
        ]);
        $this->parser->parse();

        $this->traitGenerator = $this->container->get(TraitGenerator::class);
    }

    public function test(): void
    {
        $this->traitGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/trait-ApiGen.Tests.Generator.Source.SomeTrait.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-trait-ApiGen.Tests.Generator.Source.SomeTrait.html'
        );
    }
}
