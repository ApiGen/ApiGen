<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ClassesGenerator;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassesGeneratorTest extends AbstractParserAwareTestCase
{
    public function test(): void
    {
        $this->resolveConfigurationBySource([__DIR__ . '/Source']);
        $this->parser->parse();

        /** @var ClassesGenerator $classesGenerator */
        $classesGenerator = $this->container->get(ClassesGenerator::class);
        $classesGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/classes.html');
    }
}
