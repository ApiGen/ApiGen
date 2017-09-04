<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\FunctionsGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class FunctionsGeneratorTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();

        /** @var FunctionsGenerator $functionsGenerator */
        $functionsGenerator = $this->container->get(FunctionsGenerator::class);
        $functionsGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/functions.html');
    }
}
