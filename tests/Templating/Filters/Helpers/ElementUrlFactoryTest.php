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
        $configurationMock = Mockery::mock(Configuration::class);
        $configurationMock->shouldReceive('getOption')->with(CO::TEMPLATE)->andReturn([
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
        $reflectionMethodMock->shouldReceive('isMagic')->andReturn(false);
        $reflectionMethodMock->shouldReceive('getOriginalName')->andReturnNull();
        $this->assertSame(
            'class-SomeClass#_getSomeMethod',
            $this->elementUrlFactory->createForElement($reflectionMethodMock)
        );

        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $reflectionPropertyMock->shouldReceive('isMagic')->andReturn(false);
        $this->assertSame(
            'class-SomeClass#$someProperty',
            $this->elementUrlFactory->createForElement($reflectionPropertyMock)
        );

        $reflectionConstantMock = $this->getReflectionConstantMock();
        $reflectionConstantMock->shouldReceive('getDeclaringClassName')->once()->andReturn('SomeClass');
        $this->assertSame(
            'class-SomeClass#someConstant',
            $this->elementUrlFactory->createForElement($reflectionConstantMock)
        );

        $this->assertSame(
            'function-someFunction',
            $this->elementUrlFactory->createForElement($this->getReflectionFunctionMock())
        );

        $reflectionElementMock = Mockery::mock(ElementReflectionInterface::class);
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
        $reflectionMethodMock->shouldReceive('isMagic')->andReturn(false);

        $reflectionMethodMock->shouldReceive('getOriginalName')->once()->andReturn('getSomeMethodOriginal');
        $this->assertSame(
            'class-SomeClass#_getSomeMethodOriginal',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock)
        );

        $reflectionMethodMock->shouldReceive('getOriginalName')->twice()->andReturnNull();
        $this->assertSame(
            'class-SomeClass#_getSomeMethod',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock)
        );
    }


    public function testCreateForMethodWithSeparateClass(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();
        $reflectionMethodMock->shouldReceive('getOriginalName')->andReturnNull();
        $reflectionMethodMock->shouldReceive('isMagic')->andReturn(false);

        $this->assertSame(
            'class-SomeNamespace.SomeClass#_getSomeMethod',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock, $this->getReflectionClassMock())
        );
    }


    public function testCreateForMethodWithMagicMethod(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();
        $reflectionMethodMock->shouldReceive('getOriginalName')->andReturnNull();
        $reflectionMethodMock->shouldReceive('isMagic')->andReturn(true);

        $this->assertSame(
            'class-SomeClass#m_getSomeMethod',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock)
        );
    }


    public function testCreateForProperty(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $reflectionPropertyMock->shouldReceive('isMagic')->andReturn(false);

        $this->assertSame(
            'class-SomeClass#$someProperty',
            $this->elementUrlFactory->createForProperty($reflectionPropertyMock)
        );
    }


    public function testCreateForPropertyWithSeparateClass(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $reflectionPropertyMock->shouldReceive('isMagic')->andReturn(false);

        $this->assertSame(
            'class-SomeNamespace.SomeClass#$someProperty',
            $this->elementUrlFactory->createForProperty($reflectionPropertyMock, $this->getReflectionClassMock())
        );
    }


    public function testCreateForPropertyWithMagicMethod(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $reflectionPropertyMock->shouldReceive('getOriginalName')->andReturnNull();
        $reflectionPropertyMock->shouldReceive('isMagic')->andReturn(true);
        $this->assertSame(
            'class-SomeClass#m$someProperty',
            $this->elementUrlFactory->createForProperty($reflectionPropertyMock)
        );
    }


    public function testCreateForConstant(): void
    {
        $reflectionConstantMock = $this->getReflectionConstantMock();

        $reflectionConstantMock->shouldReceive('getDeclaringClassName')->once()->andReturn('SomeClass');
        $this->assertSame(
            'class-SomeClass#someConstant',
            $this->elementUrlFactory->createForConstant($reflectionConstantMock)
        );

        $reflectionConstantMock->shouldReceive('getDeclaringClassName')->twice()->andReturnNull();
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
        $reflectionMethodMock = Mockery::mock(MethodReflectionInterface::class);
        $reflectionMethodMock->shouldReceive('getName')->andReturn('getSomeMethod');
        $reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
        return $reflectionMethodMock;
    }


    private function getReflectionClassMock(): Mockery\MockInterface
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('getName')->andReturn('SomeNamespace\\SomeClass');
        return $reflectionClassMock;
    }


    private function getReflectionPropertyMock(): Mockery\MockInterface
    {
        $reflectionPropertyMock = Mockery::mock(PropertyReflectionInterface::class);
        $reflectionPropertyMock->shouldReceive('getName')->andReturn('someProperty');
        $reflectionPropertyMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
        return $reflectionPropertyMock;
    }


    private function getReflectionConstantMock(): Mockery\MockInterface
    {
        $reflectionConstantMock = Mockery::mock(ConstantReflectionInterface::class);
        $reflectionConstantMock->shouldReceive('getName')->andReturn('someConstant');
        return $reflectionConstantMock;
    }


    private function getReflectionFunctionMock(): Mockery\MockInterface
    {
        $reflectionFunctionMock = Mockery::mock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->shouldReceive('getName')->andReturn('someFunction');
        $reflectionFunctionMock->shouldReceive('getDeclaringClassName')->andReturn('SomeClass');
        return $reflectionFunctionMock;
    }
}
