<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Parser\Reflection\Behavior\InClassInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\TokenReflection\ReflectionInterface;
use ApiGen\Parser\Elements\ElementSorter;
use PHPUnit\Framework\TestCase;

final class ElementSorterTest extends TestCase
{
    /**
     * @var ElementSorter
     */
    private $elementSorter;

    protected function setUp(): void
    {
        $this->elementSorter = new ElementSorter;
    }

    public function testSortElementsByFqnConstants(): void
    {
        $constantReflectionMock = $this->createMock(ConstantReflectionInterface::class);
        $constantReflectionMock->method('getDeclaringClassName')
            ->willReturn('B');
        $constantReflectionMock->method('getName')
            ->willReturn('C');

        $constantReflectionMock2 = $this->createMock(ConstantReflectionInterface::class);
        $constantReflectionMock2->method('getDeclaringClassName')
            ->willReturn('A');
        $constantReflectionMock2->method('getName')
            ->willReturn('D');

        $elements = [$constantReflectionMock, $constantReflectionMock2];

        $sortedElements = $this->elementSorter->sortElementsByFqn($elements);
        $this->assertNotSame($elements, $sortedElements);
        $this->assertSame($constantReflectionMock2, $sortedElements[0]);
        $this->assertSame($constantReflectionMock, $sortedElements[1]);
    }

    public function testSortElementsByFqnFunctions(): void
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('getNamespaceName')
            ->willReturn('B');
        $reflectionFunctionMock->method('getName')
            ->willReturn('C');

        $reflectionFunctionMock2 = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock2->method('getNamespaceName')
            ->willReturn('A');
        $reflectionFunctionMock2->method('getName')
            ->willReturn('D');

        $elements = [$reflectionFunctionMock, $reflectionFunctionMock2];

        $sortedElements = $this->elementSorter->sortElementsByFqn($elements);
        $this->assertNotSame($elements, $sortedElements);
        $this->assertSame($reflectionFunctionMock2, $sortedElements[0]);
        $this->assertSame($reflectionFunctionMock, $sortedElements[1]);
    }

    public function testSortElementsByFqnMethod(): void
    {
        $reflectionMethodMock = $this->createMock(
            [ReflectionInterface::class, InClassInterface::class]
        );
        $reflectionMethodMock->method('getDeclaringClassName')
            ->willReturn('B');
        $reflectionMethodMock->method('getName')
            ->willReturn('C');

        $reflectionMethodMock2 = $this->createMock(
            [ReflectionInterface::class, InClassInterface::class]
        );
        $reflectionMethodMock2->method('getDeclaringClassName')
            ->willReturn('A');
        $reflectionMethodMock2->method('getName')
            ->willReturn('D');

        $elements = [$reflectionMethodMock, $reflectionMethodMock2];

        $sortedElements = $this->elementSorter->sortElementsByFqn($elements);
        $this->assertNotSame($elements, $sortedElements);
        $this->assertSame($reflectionMethodMock2, $sortedElements[0]);
        $this->assertSame($reflectionMethodMock, $sortedElements[1]);
    }

    public function testSortElementsByFqnProperties(): void
    {
        $reflectionMethodMock = $this->createMock([ReflectionInterface::class, InClassInterface::class]);
        $reflectionMethodMock->method('getDeclaringClassName')
            ->willReturn('B');
        $reflectionMethodMock->method('getName')
            ->willReturn('C');

        $reflectionMethodMock2 = $this->createMock([ReflectionInterface::class, InClassInterface::class]);
        $reflectionMethodMock2->method('getDeclaringClassName')
            ->willReturn('A');
        $reflectionMethodMock2->method('getName')
            ->willReturn('D');

        $elements = [$reflectionMethodMock, $reflectionMethodMock2];

        $sortedElements = $this->elementSorter->sortElementsByFqn($elements);
        $this->assertNotSame($elements, $sortedElements);
        $this->assertSame($reflectionMethodMock2, $sortedElements[0]);
        $this->assertSame($reflectionMethodMock, $sortedElements[1]);
    }

    public function testSortElementsByFqnNonSupportedType(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $sortedElements = $this->elementSorter->sortElementsByFqn([$reflectionClassMock]);
        $this->assertSame([$reflectionClassMock], $sortedElements);
    }

    public function testSortElementsByFqnWithEmptyArray(): void
    {
        $this->assertSame([], $this->elementSorter->sortElementsByFqn([]));
    }
}
