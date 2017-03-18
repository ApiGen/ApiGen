<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Parser\Elements\ElementFilter;
use ApiGen\Parser\Reflection\ReflectionElement;
use Mockery;
use PHPUnit\Framework\TestCase;

class ElementFilterTest extends TestCase
{

    /**
     * @var ElementFilter
     */
    private $elementFilter;


    protected function setUp(): void
    {
        $this->elementFilter = new ElementFilter;
    }


    public function testFilterForMain(): void
    {
        $reflectionElement = $this->createMock(ReflectionElement::class);
        $reflectionElement->method('isMain')->willReturn(true);
        $reflectionElement2 = $this->createMock(ReflectionElement::class);
        $reflectionElement2->method('isMain')->willReturn(false);
        $elements = [$reflectionElement, $reflectionElement2];

        $filteredElements = $this->elementFilter->filterForMain($elements);
        $this->assertCount(1, $filteredElements);
        $this->assertInstanceOf(ReflectionElement::class, $filteredElements[0]);
    }


    public function testFilterByAnnotation(): void
    {
        $reflectionElement = $this->createMock(ReflectionElement::class);
        $reflectionElement->method('hasAnnotation')->with('todo')->willReturn(true);
        $reflectionElement->method('hasAnnotation')->with('deprecated')->willReturn(false);

        $todoElements = $this->elementFilter->filterByAnnotation([$reflectionElement], 'todo');
        $this->assertCount(1, $todoElements);
        $this->assertInstanceOf(ReflectionElement::class, $todoElements[0]);

        $this->assertCount(0, $this->elementFilter->filterByAnnotation([$reflectionElement], 'deprecated'));
    }
}
