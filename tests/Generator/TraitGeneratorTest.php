<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\TraitGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class TraitGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TraitGenerator
     */
    private $traitGenerator;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/Source']);

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
