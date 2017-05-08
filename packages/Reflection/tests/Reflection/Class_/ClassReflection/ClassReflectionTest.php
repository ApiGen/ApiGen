<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source\SomeClass;
use ApiGen\Tests\AbstractParserAwareTestCase;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

final class ClassReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var string
     */
    private $namespacePrefix = 'ApiGen\Reflection\Tests\Reflection\Class_\ClassReflection\Source';

    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $this->classReflection = $classReflections[2];
    }

    public function test(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->classReflection);
    }

    public function testName(): void
    {
        $this->assertSame(SomeClass::class, $this->classReflection->getName());
        $this->assertSame('SomeClass', $this->classReflection->getShortName());
    }

    public function testNamespace(): void
    {
        $this->assertSame($this->namespacePrefix, $this->classReflection->getNamespaceName());
    }

    public function testAnnotations(): void
    {
        $this->assertSame(
            'Huge and small' . PHP_EOL . PHP_EOL . 'description.',
            $this->classReflection->getDescription()
        );

        $this->assertTrue($this->classReflection->hasAnnotation('author'));

        $annotation = $this->classReflection->getAnnotation('author')[0];
        $this->assertInstanceOf(Author::class, $annotation);

        $this->assertCount(1, $this->classReflection->getAnnotations());
    }

    public function testLines(): void
    {
        $this->assertSame(12, $this->classReflection->getStartLine());
        $this->assertSame(23, $this->classReflection->getEndLine());
    }

    public function testIsDocumented(): void
    {
        $this->assertTrue($this->classReflection->isDocumented());
    }
}
