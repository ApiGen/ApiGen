<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Generator\TraitGenerator;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class TraitGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TraitGenerator
     */
    private $traitGenerator;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->traitGenerator = $this->container->getByType(TraitGenerator::class);
    }

    public function test(): void
    {
        $this->traitGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/trait-ApiGen.Tests.Generator.TemplateGenerators.Source.SomeTrait.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-trait-ApiGen.Tests.Generator.TemplateGenerators.Source.SomeTrait.html'
        );
    }
}
