<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\FunctionGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class FunctionGeneratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var FunctionGenerator
     */
    private $functionGenerator;

    protected function setUp(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();

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
