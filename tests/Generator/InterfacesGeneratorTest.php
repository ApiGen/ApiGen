<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\InterfacesGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class InterfacesGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        /** @var InterfacesGenerator $interfacesGenerator */
        $interfacesGenerator = $this->container->get(InterfacesGenerator::class);
        $interfacesGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/interfaces.html');
    }
}
