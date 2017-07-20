<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Partial;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Partial\AnnotationsInterface;
use ApiGen\Reflection\Tests\Reflection\Partial\Source\InheritdocClass;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class InheritTest extends AbstractParserAwareTestCase
{
    /**
     * @var AnnotationsInterface|ClassReflectionInterface
     */
    private $classReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);
        $this->classReflection = $this->reflectionStorage->getClassReflections()[InheritdocClass::class];
    }

    public function testClassDescription(): void
    {
        $this->assertSame('Some interface description.', $this->classReflection->getDescription());
    }

    public function testMethodDescription(): void
    {
        $methodReflection = $this->classReflection->getMethods()['methodWithInherit'];
        $this->assertSame('Some interface method description.', $methodReflection->getDescription());
    }
}
