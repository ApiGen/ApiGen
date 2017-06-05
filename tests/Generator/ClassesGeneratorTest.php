<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator;

use ApiGen\Generator\ClassesGenerator;
use ApiGen\Reflection\Contract\ParserInterface;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ClassesGeneratorTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var ParserInterface $parser */
        $parser = $this->container->getByType(ParserInterface::class);
        $parser->parseDirectories([__DIR__ . '/Source']);

        /** @var ClassesGenerator $classesGenerator */
        $classesGenerator = $this->container->getByType(ClassesGenerator::class);
        $classesGenerator->generate();

        $this->assertFileExists(TEMP_DIR . '/classes.html');
    }
}
