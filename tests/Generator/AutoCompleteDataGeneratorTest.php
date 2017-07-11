<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\AutoCompleteDataGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AutoCompleteDataGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $autoCompleteDataGenerator = $this->container->get(AutoCompleteDataGenerator::class);
        $autoCompleteDataGenerator->generate();

        $expectedFileName = TEMP_DIR . '/elementlist.js';
        $this->assertFileExists($expectedFileName);
        $this->assertFileEquals(__DIR__ . '/Expected/elemenlist.js', $expectedFileName);
    }
}
