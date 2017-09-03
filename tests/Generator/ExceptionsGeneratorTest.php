<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ExceptionsGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ExceptionsGeneratorTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();

        /** @var ExceptionsGenerator $exceptionsGenerator */
        $exceptionsGenerator = $this->container->get(ExceptionsGenerator::class);
        $exceptionsGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/exceptions.html');
    }
}
