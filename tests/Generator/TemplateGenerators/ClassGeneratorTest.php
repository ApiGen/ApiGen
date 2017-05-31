<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Generator\ClassGenerator;
use ApiGen\Reflection\Contract\ParserInterface;
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
            TEMP_DIR . '/class-ApiGen.Tests.Generator.TemplateGenerators.Source.SomeClass.html'
        );
        $this->assertFileExists(
            TEMP_DIR . '/source-class-ApiGen.Tests.Generator.TemplateGenerators.Source.SomeClass.html'
        );
    }
}
