<?php declare(strict_types=1);

namespace ApiGen\Tests\Generator\Resolvers\ElementResolver;

use ApiGen\Reflection\Contract\Reflection\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\ReflectionInterface;
use PHPUnit_Framework_MockObject_MockObject;

final class ClassTest extends AbstractElementResolverTest
{
    public function testGetClass(): void
    {
        $this->parserStorage->setClasses([
            'SomeClass' => $this->createClassReflection(true),
            'SomeNamespace\SomeClass' => $this->createClassReflection(true),
        ]);

        $element = $this->elementResolver->getClass('SomeClass');

        $this->assertInstanceOf(ReflectionInterface::class, $element);
        $this->assertTrue($element->isDocumented());

        $element2 = $this->elementResolver->getClass('SomeClass', 'SomeNamespace');

        $this->assertInstanceOf(ReflectionInterface::class, $element2);
        $this->assertTrue($element2->isDocumented());

        $this->assertNotSame($element, $element2);
    }

    public function testGetClassNotExisting(): void
    {
        $this->assertNull($this->elementResolver->getClass('NotExistingClass'));
    }

    public function testGetClassNotDocumented(): void
    {
        $this->parserStorage->setClasses([
            'SomeNotDocumentedClass' => $this->createClassReflection(false)
        ]);

        $this->assertNull($this->elementResolver->getClass('SomeNotDocumentedClass'));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
     */
    private function createClassReflection(bool $isDocumented)
    {
        $classReflection = $this->createMock(ClassReflectionInterface::class);
        $classReflection->method('isDocumented')
            ->willReturn($isDocumented);

        return $classReflection;
    }
}
