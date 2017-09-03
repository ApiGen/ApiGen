<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\AnnotationGroupsGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;
use ApiGen\Tests\Generator\DeprecatedSources\DeprecatedClass;
use ApiGen\Tests\Generator\DeprecatedSources\DeprecatedMethod;

final class AnnotationGroupsGeneratorTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationGroupsGenerator
     */
    private $annotationGroupsGenerator;

    protected function setUp(): void
    {
        $this->configuration->resolveOptions([
            'source' => [__DIR__ . '/DeprecatedSources'],
            'destination' => TEMP_DIR,
            'annotationGroups' => ['deprecated'],
        ]);
        $this->parser->parse();

        $this->annotationGroupsGenerator = $this->container->get(AnnotationGroupsGenerator::class);
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
