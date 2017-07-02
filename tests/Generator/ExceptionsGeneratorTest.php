<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ExceptionsGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ExceptionsGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        /** @var ExceptionsGenerator $exceptionsGenerator */
        $exceptionsGenerator = $this->container->get(ExceptionsGenerator::class);
        $exceptionsGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/exceptions.html');
    }
}
