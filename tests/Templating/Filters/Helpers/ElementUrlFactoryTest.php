<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Parser\Reflection\ReflectionElement;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use Mockery;
use PHPUnit\Framework\TestCase;

class ElementUrlFactoryTest extends TestCase
{

    /**
     * @var ElementUrlFactory
     */
    private $elementUrlFactory;


    protected function setUp(): void
    {
        $configurationMock = $this->createMock(Configuration::class);
        $configurationMock->method('getOption')->with(CO::TEMPLATE)->willReturn([
            'templates' => [
                'class' => ['filename' => 'class-%s'],
                'constant' => ['filename' => 'constant-%s'],
                'function' => ['filename' => 'function-%s']
            ]
        ]);
        $this->elementUrlFactory = new ElementUrlFactory($configurationMock);
    }


    public function testCreateForElement(): void
    {
        $this->assertSame(
            'class-SomeNamespace.SomeClass',
            $this->elementUrlFactory->createForElement($this->getReflectionClassMock())
        );

        $reflectionMethodMock = $this->getReflectionMethodMock();
        $reflectionMethodMock->method('isMagic')->willReturn(false);
        $reflectionMethodMock->method('getOriginalName')->willReturnNull();
        $this->assertSame(
            'class-SomeClass#_getSomeMethod',
            $this->elementUrlFactory->createForElement($reflectionMethodMock)
        );

        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $reflectionPropertyMock->method('isMagic')->willReturn(false);
        $this->assertSame(
            'class-SomeClass#$someProperty',
            $this->elementUrlFactory->createForElement($reflectionPropertyMock)
        );

        $reflectionConstantMock = $this->getReflectionConstantMock();
        $reflectionConstantMock->method('getDeclaringClassName')->once()->willReturn('SomeClass');
        $this->assertSame(
            'class-SomeClass#someConstant',
            $this->elementUrlFactory->createForElement($reflectionConstantMock)
        );

        $this->assertSame(
            'function-someFunction',
            $this->elementUrlFactory->createForElement($this->getReflectionFunctionMock())
        );

        $reflectionElementMock = $this->createMock(ElementReflectionInterface::class);
        $this->assertNull($this->elementUrlFactory->createForElement($reflectionElementMock));
    }


    public function testCreateForClass(): void
    {
        $this->assertSame(
            'class-SomeNamespace.SomeClass',
            $this->elementUrlFactory->createForClass($this->getReflectionClassMock())
        );
        $this->assertSame('class-SomeStringClass', $this->elementUrlFactory->createForClass('SomeStringClass'));
    }


    public function testCreateForMethod(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();
        $reflectionMethodMock->method('isMagic')->willReturn(false);

        $reflectionMethodMock->method('getOriginalName')->once()->willReturn('getSomeMethodOriginal');
        $this->assertSame(
            'class-SomeClass#_getSomeMethodOriginal',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock)
        );

        $reflectionMethodMock->method('getOriginalName')->twice()->willReturnNull();
        $this->assertSame(
            'class-SomeClass#_getSomeMethod',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock)
        );
    }


    public function testCreateForMethodWithSeparateClass(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();
        $reflectionMethodMock->method('getOriginalName')->willReturnNull();
        $reflectionMethodMock->method('isMagic')->willReturn(false);

        $this->assertSame(
            'class-SomeNamespace.SomeClass#_getSomeMethod',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock, $this->getReflectionClassMock())
        );
    }


    public function testCreateForMethodWithMagicMethod(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();
        $reflectionMethodMock->method('getOriginalName')->willReturnNull();
        $reflectionMethodMock->method('isMagic')->willReturn(true);

        $this->assertSame(
            'class-SomeClass#m_getSomeMethod',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock)
        );
    }


    public function testCreateForProperty(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $reflectionPropertyMock->method('isMagic')->willReturn(false);

        $this->assertSame(
            'class-SomeClass#$someProperty',
            $this->elementUrlFactory->createForProperty($reflectionPropertyMock)
        );
    }


    public function testCreateForPropertyWithSeparateClass(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $reflectionPropertyMock->method('isMagic')->willReturn(false);

        $this->assertSame(
            'class-SomeNamespace.SomeClass#$someProperty',
            $this->elementUrlFactory->createForProperty($reflectionPropertyMock, $this->getReflectionClassMock())
        );
    }


    public function testCreateForPropertyWithMagicMethod(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $reflectionPropertyMock->method('getOriginalName')->willReturnNull();
        $reflectionPropertyMock->method('isMagic')->willReturn(true);
        $this->assertSame(
            'class-SomeClass#m$someProperty',
            $this->elementUrlFactory->createForProperty($reflectionPropertyMock)
        );
    }


    public function testCreateForConstant(): void
    {
        $reflectionConstantMock = $this->getReflectionConstantMock();

        $reflectionConstantMock->method('getDeclaringClassName')->once()->willReturn('SomeClass');
        $this->assertSame(
            'class-SomeClass#someConstant',
            $this->elementUrlFactory->createForConstant($reflectionConstantMock)
        );

        $reflectionConstantMock->method('getDeclaringClassName')->twice()->willReturnNull();
        $this->assertSame(
            'constant-someConstant',
            $this->elementUrlFactory->createForConstant($reflectionConstantMock)
        );
    }


    public function testCreateForFunction(): void
    {
        $reflectionFunctionMock = $this->getReflectionFunctionMock();
        $this->assertSame(
            'function-someFunction',
            $this->elementUrlFactory->createForFunction($reflectionFunctionMock)
        );
    }


    private function getReflectionMethodMock(): Mockery\MockInterface
    {
        $reflectionMethodMock = $this->createMock(MethodReflectionInterface::class);
        $reflectionMethodMock->method('getName')->willReturn('getSomeMethod');
        $reflectionMethodMock->method('getDeclaringClassName')->willReturn('SomeClass');
        return $reflectionMethodMock;
    }


    private function getReflectionClassMock(): Mockery\MockInterface
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')->willReturn('SomeNamespace\\SomeClass');
        return $reflectionClassMock;
    }


    private function getReflectionPropertyMock(): Mockery\MockInterface
    {
        $reflectionPropertyMock = $this->createMock(PropertyReflectionInterface::class);
        $reflectionPropertyMock->method('getName')->willReturn('someProperty');
        $reflectionPropertyMock->method('getDeclaringClassName')->willReturn('SomeClass');
        return $reflectionPropertyMock;
    }


    private function getReflectionConstantMock(): Mockery\MockInterface
    {
        $reflectionConstantMock = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstantMock->method('getName')->willReturn('someConstant');
        return $reflectionConstantMock;
    }


    private function getReflectionFunctionMock(): Mockery\MockInterface
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('getName')->willReturn('someFunction');
        $reflectionFunctionMock->method('getDeclaringClassName')->willReturn('SomeClass');
        return $reflectionFunctionMock;
    }
}
