<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Parser\Elements\ElementFilter;
use ApiGen\Parser\Reflection\ReflectionElement;
use PHPUnit\Framework\TestCase;

final class ElementFilterTest extends TestCase
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
        $reflectionElement->method('isMain')
            ->willReturn(true);

        $reflectionElement2 = $this->createMock(ReflectionElement::class);
        $reflectionElement2->method('isMain')
            ->willReturn(false);

        $elements = [$reflectionElement, $reflectionElement2];

        $this->assertCount(1, $this->elementFilter->filterForMain($elements));
    }


    public function testFilterByAnnotation(): void
    {
        $reflectionElement = $this->createMock(ReflectionElement::class);
        $reflectionElement->method('hasAnnotation')
            ->willReturnCallback(function ($arg) {
                return $arg === 'todo';
            });

        $this->assertCount(1, $this->elementFilter->filterByAnnotation([$reflectionElement], 'todo'));
        $this->assertCount(0, $this->elementFilter->filterByAnnotation([$reflectionElement], 'deprecated'));
    }
}
