<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Configuration\Configuration;
use ApiGen\Generator\AnnotationGroupsGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\Generator\DeprecatedSources\DeprecatedClass;
use ApiGen\Tests\Generator\DeprecatedSources\DeprecatedMethod;

final class AnnotationGroupsGeneratorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AnnotationGroupsGenerator
     */
    private $annotationGroupsGenerator;

    protected function setUp(): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->resolveOptions([
            'source' => [TEMP_DIR],
            'destination' => TEMP_DIR,
            'annotationGroups' => ['deprecated']
        ]);

        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseDirectories([__DIR__ . '/DeprecatedSources']);
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
