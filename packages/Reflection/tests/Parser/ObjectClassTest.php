<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\ReflectionStorage;
use ApiGen\Reflection\Tests\Parser\Source\ChildOfObject;
use ApiGen\Reflection\Tests\Parser\Source\Object;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ObjectClassTest extends AbstractContainerAwareTestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var ReflectionStorage
     */
    private $reflectionStorage;

    public function setUp(): void
    {
        $this->parser = $this->container->get(Parser::class);
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $this->reflectionStorage = $this->container->get(ReflectionStorage::class);
    }

    public function testDirect(): void
    {
        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->assertCount(4, $classReflections);

        $this->assertArrayHasKey(Object::class, $classReflections);
        $this->assertSame(Object::class, $classReflections[Object::class]->getName());
    }

    public function testGetParent(): void
    {
        $classReflections = $this->reflectionStorage->getClassReflections();

        $classReflection = $classReflections[ChildOfObject::class]->getParentClass();
        $this->assertSame(Object::class, $classReflection->getName());
    }
}
