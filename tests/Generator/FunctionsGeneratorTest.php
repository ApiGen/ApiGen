<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\FunctionsGenerator;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class FunctionsGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        /** @var FunctionsGenerator $functionsGenerator */
        $functionsGenerator = $this->container->getByType(FunctionsGenerator::class);
        $functionsGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/functions.html');
    }
}
