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
        $reflectionElementMock = $this->createMock(ElementReflectionInterface::class);
        $reflectionElementMock->method('getName')->willReturn('ReflectionElement');
        $this->assertSame('url-for-ReflectionElement', $this->elementUrlFilters->elementUrl($reflectionElementMock));
    }


    public function testClassUrl(): void
    {
        $reflectionClassMock = $this->createMock(ClassReflectionInterface::class);
        $reflectionClassMock->method('getName')->willReturn('ReflectionClass');
        $this->assertSame('url-for-ReflectionClass', $this->elementUrlFilters->classUrl($reflectionClassMock));
    }


    public function testMethodUrl(): void
    {
        $reflectionMethodMock = $this->createMock(MethodReflectionInterface::class);
        $reflectionMethodMock->method('getName')->willReturn('ReflectionMethod');
        $this->assertSame('url-for-ReflectionMethod', $this->elementUrlFilters->methodUrl($reflectionMethodMock));
    }


    public function testPropertyUrl(): void
    {
        $reflectionPropertyMock = $this->createMock(PropertyReflectionInterface::class);
        $reflectionPropertyMock->method('getName')->willReturn('ReflectionProperty');
        $this->assertSame('url-for-ReflectionProperty', $this->elementUrlFilters->propertyUrl($reflectionPropertyMock));
    }


    public function testConstantUrl(): void
    {
        $reflectionConstantMock = $this->createMock(ConstantReflectionInterface::class);
        $reflectionConstantMock->method('getName')->willReturn('ReflectionConstant');
        $this->assertSame('url-for-ReflectionConstant', $this->elementUrlFilters->constantUrl($reflectionConstantMock));
    }


    public function testFunctionUrl(): void
    {
        $reflectionFunctionMock = $this->createMock(FunctionReflectionInterface::class);
        $reflectionFunctionMock->method('getName')->willReturn('ReflectionFunction');
        $this->assertSame('url-for-ReflectionFunction', $this->elementUrlFilters->functionUrl($reflectionFunctionMock));
    }


    private function getElementUrlFactoryMock(): Mockery\MockInterface
    {
        $elementUrlFactoryMock = $this->createMock(ElementUrlFactory::class);
        $elementUrlFactoryMock->method('createForElement')->willReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->method('createForClass')->willReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->method('createForMethod')->willReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->method('createForProperty')->willReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->method('createForConstant')->willReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        $elementUrlFactoryMock->method('createForFunction')->willReturnUsing(function (NamedInterface $arg) {
            return 'url-for-' . $arg->getName();
        });
        return $elementUrlFactoryMock;
    }
}
