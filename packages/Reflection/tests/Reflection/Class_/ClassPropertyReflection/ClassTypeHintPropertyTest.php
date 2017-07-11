<?php declare(strict_types=1);

namespace ApiGen\Reflection\Tests\Reflection\Class_\ClassPropertyReflection;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassPropertyReflection\Source\PropertyOfClassType;
use ApiGen\Reflection\Tests\Reflection\Class_\ClassPropertyReflection\Source\ReflectionProperty;
use ApiGen\Tests\AbstractParserAwareTestCase;

final class ClassTypeHintPropertyTest extends AbstractParserAwareTestCase
{
    /**
     * @var ClassPropertyReflectionInterface
     */
    private $propertyReflection;

    protected function setUp(): void
    {
        $this->parser->parseFilesAndDirectories([__DIR__ . '/Source']);

        $classReflections = $this->reflectionStorage->getClassReflections();
        $classReflection = $classReflections[ReflectionProperty::class];
        $this->propertyReflection = $classReflection->getProperty('propertyOfClassType');
    }

    public function testGetTypeHint(): void
    {
        $this->assertSame(PropertyOfClassType::class, $this->propertyReflection->getTypeHint());

        $typeHintReflection = $this->propertyReflection->getTypeHintClassOrInterfaceReflection();
        $this->assertInstanceOf(ClassReflectionInterface::class, $typeHintReflection);

        $this->assertSame(PropertyOfClassType::class, $typeHintReflection->getName());
    }
}
