<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\NamespaceGenerator;
use ApiGen\Generator\TraitGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class NamespaceGeneratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var TraitGenerator
     */
    private $namespaceGenerator;

    protected function setUp(): void
    {
        $this->configuration->resolveOptions([
            'source' => __DIR__ . '/Source',
        ]);
        $this->parser->parse();

        $this->namespaceGenerator = $this->container->get(NamespaceGenerator::class);
    }

    public function test(): void
    {
        $this->namespaceGenerator->generate();
        $this->assertFileExists(TEMP_DIR . '/namespace-ApiGen.Tests.Generator.Source.html');
    }
}
