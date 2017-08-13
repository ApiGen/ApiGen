<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Tests\Route;

use ApiGen\Reflection\Contract\Reflection\Class_\ClassConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Class_\ClassReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Function_\FunctionReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceConstantReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Interface_\InterfaceReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitMethodReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitPropertyReflectionInterface;
use ApiGen\Reflection\Contract\Reflection\Trait_\TraitReflectionInterface;
use ApiGen\StringRouting\Route\ReflectionRoute;
use ApiGen\StringRouting\StringRouter;
use ApiGen\Tests\AbstractContainerAwareTestCase;
use Throwable;

final class ReflectionRouteTest extends AbstractContainerAwareTestCase
{
    /**
     * @var StringRouter
     */
    private $stringRouter;

    protected function setUp(): void
    {
        $this->stringRouter = $this->container->get(StringRouter::class);
    }

    public function testWebalize(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')
            ->willReturn('SomeNamespace\SomeName');

        $this->assertSame(
            'class-SomeNamespace.SomeName.html',
            $this->stringRouter->buildRoute(ReflectionRoute::NAME, $reflectionClassMock)
        );
    }

    /**
     * @dataProvider provideDataForBuildRoute
     */
    public function testBasicReflection(string $reflectionInterface, string $expectedUrl): void
    {
        $reflectionClassMock = $this->createMock($reflectionInterface);
        $reflectionClassMock->method('getName')
            ->willReturn('SomeName');

        $this->assertSame(
            $expectedUrl,
            $this->stringRouter->buildRoute(ReflectionRoute::NAME, $reflectionClassMock)
        );
    }

    /**
     * @return string[][]
     */
    public function provideDataForBuildRoute(): array
    {
        return [
            [ClassReflectionInterface::class, 'class-SomeName.html'],
            [InterfaceReflectionInterface::class, 'interface-SomeName.html'],
            [TraitReflectionInterface::class, 'trait-SomeName.html'],
        ];
    }

    public function testExceptionReflection(): void
    {
        $reflectionExceptionMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionExceptionMock->method('implementsInterface')
            ->with(Throwable::class)
            ->willReturn(true);
        $reflectionExceptionMock->method('getName')
            ->willReturn('SomeException');

        $this->assertSame(
            'exception-SomeException.html',
            $this->stringRouter->buildRoute(ReflectionRoute::NAME, $reflectionExceptionMock)
        );
    }

    public function testFunctionUrl(): void
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('getName')
            ->willReturn('SomeName');

        $this->assertSame(
            'function-SomeName.html',
            $this->stringRouter->buildRoute(ReflectionRoute::NAME, $reflectionFunctionMock)
        );
    }

    /**
     * @dataProvider provideDataForBuilderClassElementRoute
     */
    public function testClassElements(string $reflectionInterface, string $expectedUrl): void
    {
        $reflectionMethodMock = $this->createMock($reflectionInterface);
        $reflectionMethodMock->method('getDeclaringClassName')
            ->willReturn('SomeClass');
        $reflectionMethodMock->method('getName')
            ->willReturn('SomeName');

        $this->assertSame(
            $expectedUrl,
            $this->stringRouter->buildRoute(ReflectionRoute::NAME, $reflectionMethodMock)
        );
    }

    /**
     * @return string[][]
     */
    public function provideDataForBuilderClassElementRoute(): array
    {
        return [
            [ClassConstantReflectionInterface::class, 'class-SomeClass.html#SomeName'],
            [ClassMethodReflectionInterface::class, 'class-SomeClass.html#_SomeName'],
            [ClassPropertyReflectionInterface::class, 'class-SomeClass.html#$SomeName'],
        ];
    }

    /**
     * @dataProvider provideDataForBuilderInterfaceElementRoute
     */
    public function testInterfaceElements(string $reflectionInterface, string $expectedUrl): void
    {
        $reflectionMock = $this->createMock($reflectionInterface);
        $reflectionMock->method('getDeclaringInterfaceName')
            ->willReturn('SomeInterface');
        $reflectionMock->method('getName')
            ->willReturn('SomeName');

        $this->assertSame($expectedUrl, $this->stringRouter->buildRoute(ReflectionRoute::NAME, $reflectionMock));
    }

    /**
     * @return string[][]
     */
    public function provideDataForBuilderInterfaceElementRoute(): array
    {
        return [
            [InterfaceConstantReflectionInterface::class, 'interface-SomeInterface.html#SomeName'],
            [InterfaceMethodReflectionInterface::class, 'interface-SomeInterface.html#_SomeName'],
        ];
    }

    /**
     * @dataProvider provideDataForBuilderTraitElementRoute
     */
    public function testTraitElements(string $reflectionInterface, string $expectedUrl): void
    {
        $reflectionMock = $this->createMock($reflectionInterface);
        $reflectionMock->method('getDeclaringTraitName')
            ->willReturn('SomeTrait');
        $reflectionMock->method('getName')
            ->willReturn('SomeName');

        $this->assertSame($expectedUrl, $this->stringRouter->buildRoute(ReflectionRoute::NAME, $reflectionMock));
    }

    /**
     * @return string[][]
     */
    public function provideDataForBuilderTraitElementRoute(): array
    {
        return [
            [TraitPropertyReflectionInterface::class, 'trait-SomeTrait.html#$SomeName'],
            [TraitMethodReflectionInterface::class, 'trait-SomeTrait.html#_SomeName'],
        ];
    }
}
