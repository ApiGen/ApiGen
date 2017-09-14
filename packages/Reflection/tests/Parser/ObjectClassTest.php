<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\Tests\Parser\Source\Object;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ObjectClassTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->get(Parser::class);
        $parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        /** @var ReflectionStorage */
        $reflectionStorage = $this->container->get(ReflectionStorage::class);

        $classReflections = $reflectionStorage->getClassReflections();
        $this->assertCount(3, $classReflections);

        $this->assertArrayHasKey(Object::class, $classReflections);
        $this->assertSame(Object::class, $classReflections[Object::class]->getName());
    }
}
