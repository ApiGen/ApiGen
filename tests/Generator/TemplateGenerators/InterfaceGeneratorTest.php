<?php declare(strict_types=1);

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Generator\TemplateGenerators\InterfaceGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class InterfaceGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var InterfaceGenerator
     */
    private $interfaceGenerator;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->interfaceGenerator = $this->container->getByType(InterfaceGenerator::class);
    }

    public function test(): void
    {
        $this->interfaceGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/interface-ApiGen.Tests.ApiGen.Generator.TemplateGenerators.Source.SomeInterface.html'
        );
    }
}
