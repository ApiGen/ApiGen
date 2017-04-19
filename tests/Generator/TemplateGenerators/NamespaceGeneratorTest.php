<?php declare(strict_types=1);

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Generator\TemplateGenerators\NamespaceGenerator;
use ApiGen\Generator\TemplateGenerators\TraitGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class NamespaceGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var TraitGenerator
     */
    private $namespaceGenerator;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->namespaceGenerator = $this->container->getByType(NamespaceGenerator::class);
    }

    public function test(): void
    {
        $this->namespaceGenerator->generate();
        $this->assertFileExists(TEMP_DIR . '/namespace-ApiGen.html');
    }
}
