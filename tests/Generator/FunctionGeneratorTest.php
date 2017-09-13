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
        $this->resolveConfigurationBySource([
        	__DIR__ . '/Source',
        	__DIR__ . '/NotLoadedSources',
        ]);
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
            TEMP_DIR . '/source-function-8cc419-SomeFunction.php.html'
        );
        $this->assertContains(
            'ApiGen\Tests\Generator\Source',
            file_get_contents(TEMP_DIR . '/source-function-8cc419-SomeFunction.php.html')
        );

        $this->assertFileExists(
            TEMP_DIR . '/source-function-859aae-SomeFunction.php.html'
        );
        $this->assertContains(
            'ApiGen\Tests\Generator\NotLoadedSources',
            file_get_contents(TEMP_DIR . '/source-function-859aae-SomeFunction.php.html')
        );

        $this->assertFileExists(
            TEMP_DIR . '/source-function-7c21e1-SubNamespace.SomeFunction.php.html'
        );
    }
}
