<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Generator\TemplateGenerators\SourceCodeGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class SourceCodeGeneratorTest // extends AbstractContainerAwareTestCase
{
    /**
     * @var SourceCodeGenerator
     */
    private $sourceCodeGenerator;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/SourceCodeSource']);

        $this->sourceCodeGenerator = $this->container->getByType(SourceCodeGenerator::class);
    }

    public function testGenerate(): void
    {
        $this->sourceCodeGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/source-class-ApiGen.Tests.Generator.TemplateGenerators.SourceCodeSource.SomeClass.html'
        );
    }
}
