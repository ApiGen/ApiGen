<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Contracts\Parser\Reflection\Behavior\NamedInterface;
use ApiGen\Contracts\Parser\Reflection\ClassReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ConstantReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\FunctionReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\MethodReflectionInterface;
use ApiGen\Contracts\Parser\Reflection\PropertyReflectionInterface;
use ApiGen\Templating\Filters\ElementUrlFilters;
use ApiGen\Templating\Filters\Helpers\ElementUrlFactory;
use Mockery;
use PHPUnit\Framework\TestCase;

class ElementUrlFiltersTest extends TestCase
{

    /**
     * @var ElementUrlFilters
     */
    private $elementUrlFilters;


    protected function setUp(): void
    {
        $this->elementUrlFilters = new ElementUrlFilters($this->getElementUrlFactoryMock());
    }


    public function testElementUrl(): void
    {
        $reflectionElementMock = Mockery::mock(ElementReflectionInterface::class);
        $reflectionElementMock->shouldReceive('getName')->andReturn('ReflectionElement');
        $this->assertSame('url-for-ReflectionElement', $this->elementUrlFilters->elementUrl($reflectionElementMock));
    }


    public function testClassUrl(): void
    {
        $reflectionClassMock = Mockery::mock(ClassReflectionInterface::class);
        $reflectionClassMock->shouldReceive('getName')->andReturn('ReflectionClass');
        $this->assertSame('url-for-ReflectionClass', $this->elementUrlFilters->classUrl($reflectionClassMock));
    }


    public function testMethodUrl(): void
    {
        $reflectionMethodMock = Mockery::mock(MethodReflectionInterface::class);
        $reflectionMethodMock->shouldReceive('getName')->andReturn('ReflectionMethod');
        $this->assertSame('url-for-ReflectionMethod', $this->elementUrlFilters->methodUrl($reflectionMethodMock));
    }


    public function testPropertyUrl(): void
    {
        $reflectionPropertyMock = Mockery::mock(PropertyReflectionInterface::class);
        $reflectionPropertyMock->shouldReceive('getName')->andReturn('ReflectionProperty');
        $this->assertSame('url-for-ReflectionProperty', $this->elementUrlFilters->propertyUrl($reflectionPropertyMock));
    }


    public function testConstantUrl(): void
    {
        $reflectionConstantMock = Mockery::mock(ConstantReflectionInterface::class);
        $reflectionConstantMock->shouldReceive('getName')->andReturn('ReflectionConstant');
        $this->assertSame('url-for-ReflectionConstant', $this->elementUrlFilters->constantUrl($reflectionConstantMock));
    }


    public function testFunctionUrl(): void
    {
        $reflectionFunctionMock = Mockery::mock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->shouldReceive('getName')->andReturn('ReflectionFunction');
        $this->assertSame('url-for-ReflectionFunction', $this->elementUrlFilters->functionUrl($reflectionFunctionMock));
    }


    private function getElementUrlFactoryMock(): Mockery\MockInterface
    {
        $elementUrlFactoryMock = Mockery::mock(ElementUrlFactory::class);
        $elementUrlFactoryMock->shouldReceive('createForElement')->andReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->shouldReceive('createForClass')->andReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->shouldReceive('createForMethod')->andReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->shouldReceive('createForProperty')->andReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->shouldReceive('createForConstant')->andReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->shouldReceive('createForFunction')->andReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        return $elementUrlFactoryMock;
    }
}
