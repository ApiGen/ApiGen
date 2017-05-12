<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassConstantReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Tests\AbstractParserAwareTestCase;
use ApiGen\Tests\Parser\Reflection\ReflectionConstantSource\ConstantInClass;

final class ClassConstantReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassConstantReflectionInterface
     */
    private $classConstantReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $classReflection = $classReflections[ConstantInClass::class];
        $this->classConstantReflection = $classReflection->getConstant('CONSTANT_INSIDE');
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

    public function testGetTypeHint(): void
    {
        $this->assertSame('int', $this->classConstantReflection->getTypeHint());
    }
//
//    public function testGetValue(): void
//    {
//        $this->assertSame(55, $this->classConstantReflection->getValue());
//    }
//
//    public function testGetDefinition(): void
//    {
//        $this->assertSame('55', $this->classConstantReflection->getValueDefinition());
//    }
}
