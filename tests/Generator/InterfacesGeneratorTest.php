<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\InterfacesGenerator;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class InterfacesGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        /** @var InterfacesGenerator $interfacesGenerator */
        $interfacesGenerator = $this->container->getByType(InterfacesGenerator::class);
        $interfacesGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/interfaces.html');
    }
}
