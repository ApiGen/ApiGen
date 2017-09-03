<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\InterfaceGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class InterfaceGeneratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var InterfaceGenerator
     */
    private $interfaceGenerator;

    protected function setUp(): void
    {
        $this->configuration->resolveOptions([
            'source' => __DIR__ . '/Source',
        ]);
        $this->parser->parse();

        $this->interfaceGenerator = $this->container->get(InterfaceGenerator::class);
    }

    public function test(): void
    {
        $this->interfaceGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/interface-ApiGen.Tests.Generator.Source.SomeInterface.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-interface-ApiGen.Tests.Generator.Source.SomeInterface.html'
        );
    }
}
