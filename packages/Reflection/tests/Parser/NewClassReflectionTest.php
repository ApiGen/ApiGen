<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Parser;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Parser\Parser;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use ApiGen\Tests\Parser\Parser\ParserSource\SomeClass;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

final class NewClassReflectionTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ClassReflectionInterface
     */
    private $classReflection;

    protected function setUp(): void
    {
        /** @var Parser $parser */
        $parser = $this->container->getByType(Parser::class);
        $parser->parseDirectories([__DIR__ . '/../../../../tests/Parser/Parser/ParserSource']);

        $classReflections = $parser->getClassReflections();
        $this->classReflection = array_shift($classReflections);
    }

    public function test(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->classReflection);
    }

    public function testName(): void
    {
        $this->assertSame(SomeClass::class, $this->classReflection->getName());
        $this->assertSame('SomeClass', $this->classReflection->getShortName());
        $this->assertSame(SomeClass::class . '()', $this->classReflection->getPrettyName());
    }

    public function testNamespace(): void
    {
        $this->assertSame(
            'ApiGen\Tests\Parser\Parser\ParserSource',
            $this->classReflection->getNamespaceName()
        );

        $this->assertSame(
            'ApiGen\Tests\Parser\Parser\ParserSource',
            $this->classReflection->getPseudoNamespaceName()
        );
    }

    public function testAnnotations(): void
    {
        $this->assertSame(
            'Huge and small' . PHP_EOL . PHP_EOL . 'description.',
            $this->classReflection->getDescription()
        );

        $this->assertTrue($this->classReflection->hasAnnotation('author'));

        $annotation = $this->classReflection->getAnnotation('author')[0];
        $this->assertInstanceOf(Author::class,$annotation);

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
