<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\InterfacesGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class InterfacesGeneratorTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->configuration->resolveOptions([
            'source' => __DIR__ . '/Source',
        ]);
        $this->parser->parse();

        /** @var InterfacesGenerator $interfacesGenerator */
        $interfacesGenerator = $this->container->get(InterfacesGenerator::class);
        $interfacesGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/interfaces.html');
    }
}
