<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Generator\TemplateGenerators\AnnotationGroupsGenerator;
use ApiGen\Parser\Parser;
use ApiGen\Templating\Template;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Latte\Engine;
use Nette\Utils\Finder;

final class AnnotationGroupsGeneratorTest extends AbstractContainerAwareTestCase
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
     * @var AnnotationGroupsGenerator
     */
    private $annotationGroupsGenerator;

    protected function setUp(): void
    {
        $this->configuration = $this->container->getByType(Configuration::class);
        $this->parser = $this->container->getByType(Parser::class);
        $this->annotationGroupsGenerator = $this->container->getByType(AnnotationGroupsGenerator::class);
    }

    public function testOptions(): void
    {
        $resolvedOptions = $this->configuration->resolveOptions([
            'source' => [TEMP_DIR],
            'destination' => TEMP_DIR . '/api',
            'annotationGroups' => ['api', 'event'],
        ]);
        $this->assertSame(['api', 'event'], $resolvedOptions[CO::ANNOTATION_GROUPS]);
    }

    public function testGenerate(): void
    {
        $this->setCorrectConfiguration();
        $this->annotationGroupsGenerator->generate();
        $this->assertFileExists(TEMP_DIR . '/api/annotation-group-deprecated.html');
    }

    public function testSetElementsWithAnnotationToTemplate(): void
    {
        $this->prepareGeneratorRequirements();

        $template = new Template(new Engine);
        $template = MethodInvoker::callMethodOnObject(
            $this->annotationGroupsGenerator,
            'setElementsWithAnnotationToTemplate',
            [$template, 'deprecated']
        );

        /** @var Template $template */
        $parameters = $template->getParameters();

        $this->assertSame('deprecated', $parameters['annotation']);
        $this->assertCount(1, $parameters['annotationClasses']);
        $this->assertCount(1, $parameters['annotationMethods']);
    }

    private function prepareGeneratorRequirements(): void
    {
        $this->setCorrectConfiguration();

        $files = [];
        foreach (Finder::findFiles('*')->in(__DIR__ . '/DeprecatedSources')->getIterator() as $file) {
            $files[] = $file;
        }

        $this->parser->parseFiles($files);
    }

    private function setCorrectConfiguration(): void
    {
        $resolvedOptions = $this->configuration->resolveOptions([
            'source' => [TEMP_DIR],
            'destination' => TEMP_DIR . '/api',
            'annotationGroups' => ['deprecated']
        ]);

        // note: new api
        $this->configuration->setOptions($resolvedOptions);
    }
}
