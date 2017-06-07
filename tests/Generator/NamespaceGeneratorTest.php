<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\NamespaceGenerator;
use ApiGen\Generator\TraitGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespaceGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TraitGenerator
     */
    private $namespaceGenerator;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->getByType(Parser::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->namespaceGenerator = $this->container->getByType(NamespaceGenerator::class);
    }

    public function test(): void
    {
        $this->namespaceGenerator->generate();
        $this->assertFileExists(TEMP_DIR . '/namespace-ApiGen.Tests.Generator.Source.html');
    }
}
