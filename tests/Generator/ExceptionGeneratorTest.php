<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ExceptionGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ExceptionGeneratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var ExceptionGenerator
     */
    private $exceptionElementGenerator;

    protected function setUp(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();

        $this->exceptionElementGenerator = $this->container->get(ExceptionGenerator::class);
    }

    public function testGenerate(): void
    {
        $this->exceptionElementGenerator->generate();

        $this->assertFileExists(
            TEMP_DIR . '/exception-ApiGen.Tests.Generator.Source.SomeException.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-exception-ApiGen.Tests.Generator.Source.SomeException.html'
        );
    }
}
