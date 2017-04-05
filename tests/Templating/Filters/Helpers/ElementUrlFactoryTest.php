<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters\Helpers;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Contracts\Configuration\ConfigurationInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

final class ElementUrlFactoryTest extends TestCase
{
    /**
     * @var ElementUrlFactory
     */
    private $elementUrlFactory;

    protected function setUp(): void
    {
        $configurationMock = $this->createMock(ConfigurationInterface::class);
        $configurationMock->method('getOption')
            ->with(CO::TEMPLATE)
            ->willReturn([
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
        $reflectionMethodMock->method('getOriginalName')
            ->willReturn('');

        $this->assertSame(
            'class-SomeClass#_getSomeMethod',
            $this->elementUrlFactory->createForElement($reflectionMethodMock)
        );

        $reflectionPropertyMock = $this->getReflectionPropertyMock();
        $this->assertSame(
            'class-SomeClass#$someProperty',
            $this->elementUrlFactory->createForElement($reflectionPropertyMock)
        );

        $reflectionConstantMock = $this->getReflectionConstantMock();
        $reflectionConstantMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');

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

        $reflectionMethodMock->method('getOriginalName')
            ->willReturn('getSomeMethodOriginal');
        $this->assertSame(
            'class-SomeClass#_getSomeMethodOriginal',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock)
        );
    }

    public function testCreateForMethodAnother(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();

        $reflectionMethodMock->method('getOriginalName')
            ->willReturn('');

        $this->assertSame(
            'class-SomeClass#_getSomeMethod',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock)
        );
    }

    public function testCreateForMethodWithSeparateClass(): void
    {
        $reflectionMethodMock = $this->getReflectionMethodMock();
        $reflectionMethodMock->method('getOriginalName')
            ->willReturn('');

        $this->assertSame(
            'class-SomeNamespace.SomeClass#_getSomeMethod',
            $this->elementUrlFactory->createForMethod($reflectionMethodMock, $this->getReflectionClassMock())
        );
    }

    public function testCreateForProperty(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();

        $this->assertSame(
            'class-SomeClass#$someProperty',
            $this->elementUrlFactory->createForProperty($reflectionPropertyMock)
        );
    }

    public function testCreateForPropertyWithSeparateClass(): void
    {
        $reflectionPropertyMock = $this->getReflectionPropertyMock();

        $this->assertSame(
            'class-SomeNamespace.SomeClass#$someProperty',
            $this->elementUrlFactory->createForProperty($reflectionPropertyMock, $this->getReflectionClassMock())
        );
    }

    public function testCreateForConstantInClass(): void
    {
        $reflectionConstantMock = $this->getReflectionConstantMock();
        $reflectionConstantMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');

        $this->assertSame(
            'class-SomeClass#someConstant',
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

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|MethodReflectionInterface
     */
    private function getReflectionMethodMock()
    {
        $reflectionMethodMock = $this->createMock(MethodReflectionInterface::class);
        $reflectionMethodMock->method('getName')
            ->willReturn('getSomeMethod');
        $reflectionMethodMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');

        return $reflectionMethodMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|ClassReflectionInterface
     */
    private function getReflectionClassMock()
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')
            ->willReturn('SomeNamespace\\SomeClass');
        return $reflectionClassMock;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|PropertyReflectionInterface
     */
    private function getReflectionPropertyMock()
    {
        $reflectionPropertyMock = $this->createMock(PropertyReflectionInterface::class);
        $reflectionPropertyMock->method('getName')
            ->willReturn('someProperty');
        $reflectionPropertyMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');
        return $reflectionPropertyMock;
    }

    private function getReflectionConstantMock(): ConstantReflectionInterface
    {
        $reflectionConstantMock = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstantMock->method('getName')
            ->willReturn('someConstant');

        return $reflectionConstantMock;
    }

    private function getReflectionFunctionMock(): FunctionReflectionInterface
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('getName')
            ->willReturn('someFunction');

        return $reflectionFunctionMock;
    }
}
