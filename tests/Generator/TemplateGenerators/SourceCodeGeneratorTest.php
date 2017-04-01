<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\TemplateGenerators\SourceCodeGenerator;
use ApiGen\Parser\Elements\ElementStorage;
use ApiGen\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Nette\Utils\Finder;

class SourceCodeGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var SourceCodeGenerator
     */
    private $sourceCodeGenerator;

    /**
     * @var ElementStorage
     */
    private $elementStorage;

    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(Configuration::class);
        $this->parser = $this->container->getByType(Parser::class);
        $this->sourceCodeGenerator = $this->container->getByType(SourceCodeGenerator::class);
        $this->elementStorage = $this->container->getByType(ElementStorage::class);
    }

    public function testStepCount(): void
    {
        $this->prepareSourceCodeGenerator();
        $this->assertSame(1, $this->sourceCodeGenerator->getStepCount());
    }

    public function testGenerate(): void
    {
        $this->prepareSourceCodeGenerator();
        $this->sourceCodeGenerator->generate();
        $this->assertFileExists(
            TEMP_DIR . '/api/source-class-ApiGen.Tests.Generator.TemplateGenerators.SourceCodeSource.SomeClass.html'
        );
    }

    public function testGenerateForElement(): void
    {
        $this->prepareSourceCodeGenerator();
        $classes = $this->elementStorage->getClasses();
        $classElement = array_pop($classes);

        MethodInvoker::callMethodOnObject($this->sourceCodeGenerator, 'generateForElement', [$classElement]);
        $this->assertFileExists(
            TEMP_DIR . '/api/source-class-ApiGen.Tests.Generator.TemplateGenerators.SourceCodeSource.SomeClass.html'
        );
    }

    private function prepareSourceCodeGenerator(): void
    {
        $this->configuration->resolveOptions([
            'source' => [__DIR__ . '/SourceCodeSource'],
            'destination' => TEMP_DIR . '/api'
        ]);

        $files = [];
        foreach (Finder::findFiles('*')->in(__DIR__ . '/SourceCodeSource')->getIterator() as $file) {
            $files[] = $file;
        }

        $this->parser->parseFiles($files);
    }
}
