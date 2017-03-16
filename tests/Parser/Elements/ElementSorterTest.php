<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Parser\Elements\ElementSorter;
use ApiGen\Parser\Reflection\ReflectionClass;
use ApiGen\Parser\Reflection\ReflectionConstant;
use ApiGen\Parser\Reflection\ReflectionFunction;
use ApiGen\Parser\Reflection\ReflectionMethod;
use Mockery;
use PHPUnit\Framework\TestCase;

class ElementSorterTest extends TestCase
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
        $reflectionConstantMock = Mockery::mock(ReflectionConstant::class);
        $reflectionConstantMock->shouldReceive('getDeclaringClassName')->andReturn('B');
        $reflectionConstantMock->shouldReceive('getName')->andReturn('C');

        $reflectionConstantMock2 = Mockery::mock(ReflectionConstant::class);
        $reflectionConstantMock2->shouldReceive('getDeclaringClassName')->andReturn('A');
        $reflectionConstantMock2->shouldReceive('getName')->andReturn('D');

        $elements = [$reflectionConstantMock, $reflectionConstantMock2];

        $sortedElements = $this->elementSorter->sortElementsByFqn($elements);
        $this->assertNotSame($elements, $sortedElements);
        $this->assertSame($reflectionConstantMock2, $sortedElements[0]);
        $this->assertSame($reflectionConstantMock, $sortedElements[1]);
    }


    public function testSortElementsByFqnFunctions(): void
    {
        $reflectionFunctionMock = Mockery::mock(ReflectionFunction::class);
        $reflectionFunctionMock->shouldReceive('getNamespaceName')->andReturn('B');
        $reflectionFunctionMock->shouldReceive('getName')->andReturn('C');

        $reflectionFunctionMock2 = Mockery::mock(ReflectionFunction::class);
        $reflectionFunctionMock2->shouldReceive('getNamespaceName')->andReturn('A');
        $reflectionFunctionMock2->shouldReceive('getName')->andReturn('D');

        $elements = [$reflectionFunctionMock, $reflectionFunctionMock2];

        $sortedElements = $this->elementSorter->sortElementsByFqn($elements);
        $this->assertNotSame($elements, $sortedElements);
        $this->assertSame($reflectionFunctionMock2, $sortedElements[0]);
        $this->assertSame($reflectionFunctionMock, $sortedElements[1]);
    }


    public function testSortElementsByFqnMethod(): void
    {
        $reflectionMethodMock = Mockery::mock(ReflectionMethod::class);
        $reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturn('B');
        $reflectionMethodMock->shouldReceive('getName')->andReturn('C');

        $reflectionMethodMock2 = Mockery::mock(ReflectionMethod::class);
        $reflectionMethodMock2->shouldReceive('getDeclaringClassName')->andReturn('A');
        $reflectionMethodMock2->shouldReceive('getName')->andReturn('D');

        $elements = [$reflectionMethodMock, $reflectionMethodMock2];

        $sortedElements = $this->elementSorter->sortElementsByFqn($elements);
        $this->assertNotSame($elements, $sortedElements);
        $this->assertSame($reflectionMethodMock2, $sortedElements[0]);
        $this->assertSame($reflectionMethodMock, $sortedElements[1]);
    }


    public function testSortElementsByFqnProperties(): void
    {
        $reflectionMethodMock = Mockery::mock('ApiGen\Parser\Reflection\ReflectionMethod');
        $reflectionMethodMock->shouldReceive('getDeclaringClassName')->andReturn('B');
        $reflectionMethodMock->shouldReceive('getName')->andReturn('C');

        $reflectionMethodMock2 = Mockery::mock('ApiGen\Parser\Reflection\ReflectionMethod');
        $reflectionMethodMock2->shouldReceive('getDeclaringClassName')->andReturn('A');
        $reflectionMethodMock2->shouldReceive('getName')->andReturn('D');

        $elements = [$reflectionMethodMock, $reflectionMethodMock2];

        $sortedElements = $this->elementSorter->sortElementsByFqn($elements);
        $this->assertNotSame($elements, $sortedElements);
        $this->assertSame($reflectionMethodMock2, $sortedElements[0]);
        $this->assertSame($reflectionMethodMock, $sortedElements[1]);
    }


    public function testSortElementsByFqnNonSupportedType(): void
    {
        $reflectionClassMock = Mockery::mock(ReflectionClass::class);
        $sortedElements = $this->elementSorter->sortElementsByFqn([$reflectionClassMock]);
        $this->assertSame([$reflectionClassMock], $sortedElements);
    }


    public function testSortElementsByFqnWithEmptyArray(): void
    {
        $this->assertSame([], $this->elementSorter->sortElementsByFqn([]));
    }
}
