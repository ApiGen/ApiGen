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


    protected function setUp()
    {
        $this->elementFilter = new ElementFilter;
    }


    public function testFilterForMain()
    {
        $reflectionElement = Mockery::mock(ReflectionElement::class);
        $reflectionElement->shouldReceive('isMain')->andReturn(true);
        $reflectionElement2 = Mockery::mock(ReflectionElement::class);
        $reflectionElement2->shouldReceive('isMain')->andReturn(false);
        $elements = [$reflectionElement, $reflectionElement2];

        $filteredElements = $this->elementFilter->filterForMain($elements);
        $this->assertCount(1, $filteredElements);
        $this->assertInstanceOf(ReflectionElement::class, $filteredElements[0]);
    }


    public function testFilterByAnnotation()
    {
        $reflectionElement = Mockery::mock(ReflectionElement::class);
        $reflectionElement->shouldReceive('hasAnnotation')->with('todo')->andReturn(true);
        $reflectionElement->shouldReceive('hasAnnotation')->with('deprecated')->andReturn(false);

        $todoElements = $this->elementFilter->filterByAnnotation([$reflectionElement], 'todo');
        $this->assertCount(1, $todoElements);
        $this->assertInstanceOf(ReflectionElement::class, $todoElements[0]);

        $this->assertCount(0, $this->elementFilter->filterByAnnotation([$reflectionElement], 'deprecated'));
    }
}
