<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\FunctionsGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class FunctionsGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        /** @var FunctionsGenerator $functionsGenerator */
        $functionsGenerator = $this->container->get(FunctionsGenerator::class);
        $functionsGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/functions.html');
    }
}
