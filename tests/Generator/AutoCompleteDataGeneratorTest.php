<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\AutoCompleteDataGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class AutoCompleteDataGeneratorTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();

        $autoCompleteDataGenerator = $this->container->get(AutoCompleteDataGenerator::class);
        $autoCompleteDataGenerator->generate();

        $expectedFileName = TEMP_DIR . '/elementlist.js';
        $this->assertFileExists($expectedFileName);
        $this->assertFileEquals(__DIR__ . '/Expected/elemenlist.js', $expectedFileName);
    }
}
