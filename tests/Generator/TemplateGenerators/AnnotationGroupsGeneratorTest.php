<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\TemplateGenerators;

use ApiGen\Configuration\Configuration;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\ParserInterface;
use ApiGen\Generator\TemplateGenerators\AnnotationGroupsGenerator;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\Generator\TemplateGenerators\DeprecatedSources\DeprecatedClass;
use ApiGen\Tests\Generator\TemplateGenerators\DeprecatedSources\DeprecatedMethod;

final class AnnotationGroupsGeneratorTest extends AbstractContainerAwareTestCase
{
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

        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/DeprecatedSources']);
        $this->annotationGroupsGenerator = $this->container->getByType(AnnotationGroupsGenerator::class);
    }

    public function testGenerate(): void
    {
        $this->annotationGroupsGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/annotation-group-deprecated.html');
        $this->assertContains(
            DeprecatedClass::class,
                file_get_contents(TEMP_DIR . '/annotation-group-deprecated.html')
        );
        $this->assertContains(
            DeprecatedMethod::class,
            file_get_contents(TEMP_DIR . '/annotation-group-deprecated.html')
        );
    }
}
