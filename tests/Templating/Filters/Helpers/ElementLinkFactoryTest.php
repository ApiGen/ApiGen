<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementLinkFactory;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use ApiGen\Templating\Filters\Helpers\LinkBuilder;
use PHPUnit\Framework\TestCase;

class ElementLinkFactoryTest extends TestCase
{

    /**
     * @var ElementLinkFactory
     */
    private $elementLinkFactory;


    protected function setUp(): void
    {
        $this->elementLinkFactory = new ElementLinkFactory($this->getElementUrlFactoryMock(), new LinkBuilder);
    }


    public function testCreateForElementClass(): void
    {
        $reflectionClass = $this->createMock(ClassReflectionInterface::class);
        $reflectionClass->method('getName')->willReturn('SomeClass');
        $reflectionClass->method('getDeclaringClassName')->willReturn('declaringClass');

        $this->assertSame(
            '<a href="class-link-SomeClass">SomeClass</a>',
            $this->elementLinkFactory->createForElement($reflectionClass)
        );
    }


    public function testCreateForFunction(): void
    {
        $reflectionFunction = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunction->method('getName')->willReturn('getSome');
        $reflectionFunction->method('getDeclaringClassName')->willReturn('DeclaringClass');

        $this->assertSame(
            '<a href="function-link-getSome">getSome()</a>',
            $this->elementLinkFactory->createForElement($reflectionFunction)
        );
    }


    public function testCreateForConstant(): void
    {
        $reflectionConstant = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstant->method('getName')->willReturn('SOME_CONSTANT');
        $reflectionConstant->method('getDeclaringClassName')->willReturnNull();
        $reflectionConstant->method('inNamespace')->willReturn(false);

        $this->assertSame(
            '<a href="constant-link-SOME_CONSTANT"><b>SOME_CONSTANT</b></a>',
            $this->elementLinkFactory->createForElement($reflectionConstant)
        );
    }


    public function testCreateForConstantInClass(): void
    {
        $reflectionConstant = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstant->method('getName')->willReturn('SOME_CONSTANT');
        $reflectionConstant->method('getDeclaringClassName')->willReturn('DeclaringClass');

        $this->assertSame(
            '<a href="constant-link-SOME_CONSTANT">DeclaringClass::<b>SOME_CONSTANT</b></a>',
            $this->elementLinkFactory->createForElement($reflectionConstant)
        );
    }


    public function testCreateForElementConstantInNamespace(): void
    {
        $reflectionConstant = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstant->method('getName')->willReturn('SOME_CONSTANT');
        $reflectionConstant->method('getShortName')->willReturn('SHORT_SOME_CONSTANT');
        $reflectionConstant->method('getDeclaringClassName')->willReturnNull();
        $reflectionConstant->method('inNamespace')->willReturn(true);
        $reflectionConstant->method('getNamespaceName')->willReturn('Namespace');

        $this->assertSame(
            '<a href="constant-link-SOME_CONSTANT">Namespace\<b>SHORT_SOME_CONSTANT</b></a>',
            $this->elementLinkFactory->createForElement($reflectionConstant)
        );
    }


    public function testCreateForProperty(): void
    {
        $reflectionProperty = $this->createMock(PropertyReflectionInterface::class);
        $reflectionProperty->method('getName')->willReturn('property');
        $reflectionProperty->method('getDeclaringClassName')->willReturn('SomeClass');

        $this->assertSame(
            '<a href="property-link-property">SomeClass::<var>$property</var></a>',
            $this->elementLinkFactory->createForElement($reflectionProperty)
        );
    }


    public function testCreateForMethod(): void
    {
        $reflectionMethod = $this->createMock(MethodReflectionInterface::class);
        $reflectionMethod->method('getName')->willReturn('method');
        $reflectionMethod->method('getDeclaringClassName')->willReturn('SomeClass');

        $this->assertSame(
            '<a href="method-link-method">SomeClass::method()</a>',
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
        $reflectionClass->method('getName')->willReturn('SomeClass');
        $reflectionClass->method('getDeclaringClassName')->willReturn('someElement');

        $this->assertSame(
            '<a href="class-link-SomeClass" class="deprecated">SomeClass</a>',
            $this->elementLinkFactory->createForElement($reflectionClass, ['deprecated'])
        );
    }


    private function getElementUrlFactoryMock(): Mockery\MockInterface
    {
        $elementUrlFactoryMock = $this->createMock(ElementUrlFactory::class);
        $elementUrlFactoryMock->method('createForClass')->willReturnCallback(
            function (NamedInterface $reflectionClass) {
                return 'class-link-' . $reflectionClass->getName();
            }
        );
        $elementUrlFactoryMock->method('createForConstant')->willReturnCallback(
            function (NamedInterface $reflectionConstant) {
                return 'constant-link-' . $reflectionConstant->getName();
            }
        );
        $elementUrlFactoryMock->method('createForFunction')->willReturnCallback(
            function (NamedInterface $reflectionFunction) {
                return 'function-link-' . $reflectionFunction->getName();
            }
        );
        $elementUrlFactoryMock->method('createForProperty')->willReturnCallback(
            function (NamedInterface $reflectionProperty) {
                return 'property-link-' . $reflectionProperty->getName();
            }
        );
        $elementUrlFactoryMock->method('createForMethod')->willReturnCallback(
            function (NamedInterface $reflectionMethod) {
                return 'method-link-' . $reflectionMethod->getName();
            }
        );
        return $elementUrlFactoryMock;
    }
}
