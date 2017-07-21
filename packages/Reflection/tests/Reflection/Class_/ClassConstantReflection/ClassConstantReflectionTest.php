<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassConstantReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassConstantReflection\Source\ConstantInClass;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassConstantReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassConstantReflectionInterface
     */
    private $classConstantReflection;

    /**
     * @var ClassConstantReflectionInterface
     */
    private $composedClassConstantReflection;

    /**
     * @var ClassConstantReflectionInterface
     */
    private $arrayClassConstantReflection;

    /**
     * @var ClassConstantReflectionInterface
     */
    private $composedWithDirConstantReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $classReflection = $classReflections[ConstantInClass::class];
        $this->classConstantReflection = $classReflection->getConstant('CONSTANT_INSIDE');
        $this->composedClassConstantReflection = $classReflection->getConstant('COMPOSED');
        $this->arrayClassConstantReflection = $classReflection->getConstant('ARRAY_CONSTANT');
        $this->composedWithDirConstantReflection = $classReflection->getConstant('COMPOSED_WITH_DIR');
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(ClassConstantReflectionInterface::class, $this->classConstantReflection);
    }

    public function testGetDeclaringClass(): void
    {
        $this->assertInstanceOf(ClassReflectionInterface::class, $this->classConstantReflection->getDeclaringClass());
        $this->assertSame(ConstantInClass::class, $this->classConstantReflection->getDeclaringClassName());
    }

    public function testGetName(): void
    {
        $this->assertSame('CONSTANT_INSIDE', $this->classConstantReflection->getName());
    }

    public function testValue(): void
    {
        $this->assertSame('int', $this->classConstantReflection->getTypeHint());
        $this->assertSame(55, $this->classConstantReflection->getValue());
        $this->assertSame('right now', $this->composedClassConstantReflection->getValue());
        $this->assertSame(__DIR__ . '/Source/here', $this->composedWithDirConstantReflection->getValue());

        $this->assertSame([1, 2], $this->arrayClassConstantReflection->getValue());
    }

    public function testLines(): void
    {
        $this->assertSame(12, $this->classConstantReflection->getStartLine());
        $this->assertSame(12, $this->classConstantReflection->getEndLine());
    }

    public function testVisibility(): void
    {
        $this->assertTrue($this->classConstantReflection->isPublic());
        $this->assertFalse($this->classConstantReflection->isProtected());
        $this->assertFalse($this->classConstantReflection->isPrivate());
    }

    public function testAnnotations(): void
    {
        $this->assertSame('Nice description.', $this->classConstantReflection->getDescription());
    }
}
