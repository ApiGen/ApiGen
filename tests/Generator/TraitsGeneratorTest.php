<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\TraitsGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class TraitsGeneratorTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();

        /** @var TraitsGenerator $traitsGenerator */
        $traitsGenerator = $this->container->get(TraitsGenerator::class);
        $traitsGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/traits.html');
    }
}
