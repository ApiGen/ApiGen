<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassMethodReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassMethodReflection\Source\ReflectionMethod;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassMethodReflectionTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassMethodReflectionInterface
     */
    private $methodReflection;

    protected function setUp(): void
    {
        $this->parser->parseDirectories([__DIR__ . '/Source']);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $classReflection = $classReflections[ReflectionMethod::class];
        $this->methodReflection = $classReflection->getMethod('methodWithArgs');
    }

    public function testName()
    {
        $this->assertSame('methodWithArgs', $this->methodReflection->getName());
    }

    public function testInstance(): void
    {
        $this->assertInstanceOf(ClassMethodReflectionInterface::class, $this->methodReflection);
    }
}
