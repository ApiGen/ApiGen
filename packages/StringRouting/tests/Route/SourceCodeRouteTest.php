<?php declare(strict_types=1);

namespace ApiGen\StringRouting\Tests\Route;

use ApiGen\Configuration\Configuration;
use ApiGen\ModularConfiguration\Option\DestinationOption;
use ApiGen\ModularConfiguration\Option\SourceOption;
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
use ApiGen\StringRouting\Route\SourceCodeRoute;
use ApiGen\StringRouting\StringRouter;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class SourceCodeRouteTest extends AbstractContainerAwareTestCase
{
    /**
     * @var StringRouter
     */
    private $stringRouter;

    protected function setUp(): void
    {
        $this->stringRouter = $this->container->get(StringRouter::class);
        /** @var Configuration $configuration */
        $configuration = $this->container->get(Configuration::class);
        $configuration->resolveOptions([
            SourceOption::NAME => [__DIR__],
            DestinationOption::NAME => TEMP_DIR,
        ]);
    }

    public function testWebalize(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')
            ->willReturn('SomeNamespace\SomeName');

        $this->assertSame(
            'source-class-SomeNamespace.SomeName.html',
            $this->stringRouter->buildRoute(SourceCodeRoute::NAME, $reflectionClassMock)
        );
    }

    /**
     * @dataProvider provideDataForBuildRoute
     */
    public function testBasicReflection(string $reflectionInterface, string $expectedUrl): void
    {
        $reflectionMock = $this->createMock($reflectionInterface);
        $reflectionMock->method('getName')
            ->willReturn('SomeName');

        $this->assertSame($expectedUrl, $this->stringRouter->buildRoute(SourceCodeRoute::NAME, $reflectionMock));
    }

    /**
     * @return string[][]
     */
    public function provideDataForBuildRoute(): array
    {
        return [
            [ClassReflectionInterface::class, 'source-class-SomeName.html'],
            [InterfaceReflectionInterface::class, 'source-interface-SomeName.html'],
            [TraitReflectionInterface::class, 'source-trait-SomeName.html'],
        ];
    }

    /**
     * @dataProvider provideDataForBuildLinedRoute
     */
    public function testLinedReflection(string $reflectionInterface, string $expectedUrl): void
    {
        $reflectionMock = $this->createMock($reflectionInterface);
        $reflectionMock->method('getName')
            ->willReturn('SomeName');
        $reflectionMock->method('getFileName')
            ->willReturn(__FILE__);
        $reflectionMock->method('getStartLine')
            ->willReturn(15);
        $reflectionMock->method('getEndLine')
            ->willReturn(25);

        $this->assertSame($expectedUrl, $this->stringRouter->buildRoute(SourceCodeRoute::NAME, $reflectionMock));
    }

    /**
     * @return string[][]
     */
    public function provideDataForBuildLinedRoute(): array
    {
        return [
            [FunctionReflectionInterface::class, 'source-function-SourceCodeRouteTest.php.html#15-25'],
        ];
    }

    /**
     * @dataProvider provideDataForBuilderClassElementRoute
     */
    public function testClassElements(string $reflectionInterface, string $expectedUrl): void
    {
        $reflectionMock = $this->createMock($reflectionInterface);
        $reflectionMock->method('getDeclaringClassName')
            ->willReturn('someClass');
        $reflectionMock->method('getStartLine')
            ->willReturn(20);
        $reflectionMock->method('getEndLine')
            ->willReturn(30);

        $this->assertSame($expectedUrl, $this->stringRouter->buildRoute(SourceCodeRoute::NAME, $reflectionMock));
    }

    /**
     * @return string[][]
     */
    public function provideDataForBuilderClassElementRoute(): array
    {
        return [
            [ClassConstantReflectionInterface::class, 'source-class-someClass.html#20-30'],
            [ClassMethodReflectionInterface::class, 'source-class-someClass.html#20-30'],
            [ClassPropertyReflectionInterface::class, 'source-class-someClass.html#20-30'],
        ];
    }

    /**
     * @dataProvider provideDataForBuilderInterfaceElementRoute
     */
    public function testInterfaceElements(string $reflectionInterface, string $expectedUrl): void
    {
        $reflectionMock = $this->createMock($reflectionInterface);
        $reflectionMock->method('getDeclaringInterfaceName')
            ->willReturn('someInterface');
        $reflectionMock->method('getStartLine')
            ->willReturn(20);
        $reflectionMock->method('getEndLine')
            ->willReturn(30);

        $this->assertSame($expectedUrl, $this->stringRouter->buildRoute(SourceCodeRoute::NAME, $reflectionMock));
    }

    /**
     * @return string[][]
     */
    public function provideDataForBuilderInterfaceElementRoute(): array
    {
        return [
            [InterfaceConstantReflectionInterface::class, 'source-interface-someInterface.html#20-30'],
            [InterfaceMethodReflectionInterface::class, 'source-interface-someInterface.html#20-30'],
        ];
    }

    /**
     * @dataProvider provideDataForBuilderTraitElementRoute
     */
    public function testTraitElements(string $reflectionInterface, string $expectedUrl): void
    {
        $reflectionMock = $this->createMock($reflectionInterface);
        $reflectionMock->method('getDeclaringTraitName')
            ->willReturn('someTrait');
        $reflectionMock->method('getStartLine')
            ->willReturn(20);
        $reflectionMock->method('getEndLine')
            ->willReturn(30);

        $this->assertSame($expectedUrl, $this->stringRouter->buildRoute(SourceCodeRoute::NAME, $reflectionMock));
    }

    /**
     * @return string[][]
     */
    public function provideDataForBuilderTraitElementRoute(): array
    {
        return [
            [TraitPropertyReflectionInterface::class, 'source-trait-someTrait.html#20-30'],
            [TraitMethodReflectionInterface::class, 'source-trait-someTrait.html#20-30'],
        ];
    }
}
