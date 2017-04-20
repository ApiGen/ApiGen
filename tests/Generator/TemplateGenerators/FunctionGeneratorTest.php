<?php declare(strict_types=1);

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Generator\TemplateGenerators\FunctionGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class FunctionGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var FunctionGenerator
     */
    private $functionGenerator;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->functionGenerator = $this->container->getByType(FunctionGenerator::class);
    }

    public function test(): void
    {
        $this->functionGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/function-ApiGen.Tests.ApiGen.Generator.TemplateGenerators.Source.someFunction.html'
        );
    }
}
