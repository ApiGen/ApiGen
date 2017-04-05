<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementLinkFactory;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class ElementLinkFactoryTest extends AbstractContainerAwareTestCase
{
    /**
     * @var ElementLinkFactory
     */
    private $elementLinkFactory;

    protected function setUp(): void
    {
        $this->elementLinkFactory = $this->container->getByType(ElementLinkFactory::class);
    }

    public function testCreateForElementClass(): void
    {
        $reflectionClass = $this->createMock(ClassReflectionInterface::class);
        $reflectionClass->method('getName')
            ->willReturn('SomeClass');

        $this->assertSame(
            '<a href="class-SomeClass.html">SomeClass</a>',
            $this->elementLinkFactory->createForElement($reflectionClass)
        );
    }

    public function testCreateForFunction(): void
    {
        $reflectionFunction = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunction->method('getName')
            ->willReturn('getSome');

        $this->assertSame(
            '<a href="function-getSome.html">getSome()</a>',
            $this->elementLinkFactory->createForElement($reflectionFunction)
        );
    }

    public function testCreateForConstantInClass(): void
    {
        $reflectionConstant = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstant->method('getName')
            ->willReturn('SOME_CONSTANT');
        $reflectionConstant->method('getDeclaringClassName')
            ->willReturn('DeclaringClass');

        $this->assertSame(
            '<a href="class-DeclaringClass.html#SOME_CONSTANT">DeclaringClass::<b>SOME_CONSTANT</b></a>',
            $this->elementLinkFactory->createForElement($reflectionConstant)
        );
    }

    public function testCreateForProperty(): void
    {
        $reflectionProperty = $this->createMock(PropertyReflectionInterface::class);
        $reflectionProperty->method('getName')
            ->willReturn('property');
        $reflectionProperty->method('getDeclaringClassName')
            ->willReturn('SomeClass');

        $this->assertSame(
            '<a href="class-SomeClass.html#$property">SomeClass::<var>$property</var></a>',
            $this->elementLinkFactory->createForElement($reflectionProperty)
        );
    }

    public function testCreateForMethod(): void
    {
        $reflectionMethod = $this->createMock(MethodReflectionInterface::class);
        $reflectionMethod->method('getName')
            ->willReturn('method');
        $reflectionMethod->method('getDeclaringClassName')
            ->willReturn('SomeClass');

        $this->assertSame(
            '<a href="class-SomeClass.html#_method">SomeClass::method()</a>',
            $this->elementLinkFactory->createForElement($reflectionMethod)
        );
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testCreateForElementOfUnspecificType(): void
    {
        $reflectionElement = $this->createMock(ElementReflectionInterface::class);
        $this->elementLinkFactory->createForElement($reflectionElement);
    }

    public function testCreateForElementWithCssClasses(): void
    {
        $reflectionClass = $this->createMock(ClassReflectionInterface::class);
        $reflectionClass->method('getName')
            ->willReturn('SomeClass');

        $this->assertSame(
            '<a href="class-SomeClass.html" class="deprecated">SomeClass</a>',
            $this->elementLinkFactory->createForElement($reflectionClass, ['deprecated'])
        );
    }
}
