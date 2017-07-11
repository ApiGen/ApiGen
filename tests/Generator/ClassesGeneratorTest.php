<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ClassesGenerator;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ClassesGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        /** @var ClassesGenerator $classesGenerator */
        $classesGenerator = $this->container->get(ClassesGenerator::class);
        $classesGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/classes.html');
    }
}
