<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\Tests\Parser\AnotherSource\ParentClassFromAnotherSource;
use ApiGen\Reflection\Tests\Parser\NotLoadedSources\SomeClass;
use ApiGen\Reflection\Tests\Parser\Source\ClassWithParentFromAnotherSource;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class FileLocatorTest extends AbstractContainerAwareTestCase
{
    public function testSingle(): void
    {
        $parser = $this->container->get(Parser::class);

        $parser->parseFilesAndDirectories([__DIR__ . '/NotLoadedSources/SomeClass.php']);

        $this->assertTrue(class_exists(SomeClass::class));
    }

    public function testMultiple(): void
    {
        $parser = $this->container->get(Parser::class);

        $parser->parseFilesAndDirectories([
            __DIR__ . '/Source/ClassWithParentFromAnotherSource.php',
            __DIR__ . '/AnotherSource/ParentClassFromAnotherSource.php'
        ]);

        $this->assertTrue(class_exists(ClassWithParentFromAnotherSource::class));
        $this->assertTrue(class_exists(ParentClassFromAnotherSource::class));
    }
}
