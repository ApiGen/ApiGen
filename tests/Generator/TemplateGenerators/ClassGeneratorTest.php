<?php declare(strict_types=1);

namespace ApiGen\Tests\ApiGen\Generator\TemplateGenerators;

use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Generator\TemplateGenerators\ClassGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ClassGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ClassGenerator
     */
    private $classElementGenerator;

    protected function setUp(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        $this->classElementGenerator = $this->container->getByType(ClassGenerator::class);
    }

    public function testGenerate(): void
    {
        $this->classElementGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/class-ApiGen.Tests.ApiGen.Generator.TemplateGenerators.Source.SomeClass.html'
        );
    }
}
