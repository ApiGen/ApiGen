<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\InterfaceGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class InterfaceGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var InterfaceGenerator
     */
    private $interfaceGenerator;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->interfaceGenerator = $this->container->get(InterfaceGenerator::class);
    }

    public function test(): void
    {
        $this->interfaceGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/interface-ApiGen.Tests.Generator.Source.SomeInterface.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-interface-ApiGen.Tests.Generator.Source.SomeInterface.html'
        );
    }
}
