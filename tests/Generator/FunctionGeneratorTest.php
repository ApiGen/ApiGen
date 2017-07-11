<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\FunctionGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class FunctionGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var FunctionGenerator
     */
    private $functionGenerator;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $this->functionGenerator = $this->container->get(FunctionGenerator::class);
    }

    public function test(): void
    {
        $this->functionGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/function-ApiGen.Tests.Generator.Source.someFunction.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/function-ApiGen.Tests.Generator.Source.someOtherFunction.html'
        );

        $this->assertFileExists(
            TEMP_DIR . '/source-function-Generator.Source.SomeFunction.php.html'
        );
    }
}
