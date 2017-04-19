<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Generator\TemplateGenerators\AnnotationGroupsGenerator;
use ApiGen\Templating\Template;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\MethodInvoker;
use Latte\Engine;

final class AnnotationGroupsGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var AnnotationGroupsGenerator
     */
    private $annotationGroupsGenerator;

    protected function setUp(): void
    {
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->container->getByType(Configuration::class);
        $configuration->resolveOptions([
            'source' => [TEMP_DIR],
            'destination' => TEMP_DIR,
            'annotationGroups' => ['deprecated']
        ]);

        $this->parser = $this->container->getByType(ParserInterface::class);
        $this->annotationGroupsGenerator = $this->container->getByType(AnnotationGroupsGenerator::class);
    }

    public function testGenerate(): void
    {
        $this->annotationGroupsGenerator->generate();
        $this->assertFileExists(TEMP_DIR . '/annotation-group-deprecated.html');
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
        $this->parser->parseDirectories([__DIR__ . '/DeprecatedSources']);
    }
}
