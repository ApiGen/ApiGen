<?php declare(strict_types=1);

namespace ApiGen\Parser\Tests\Elements;

use ApiGen\Contracts\Parser\Reflection\ElementReflectionInterface;
use ApiGen\Parser\Elements\ElementFilter;
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

    public function testFilterByAnnotation(): void
    {
        $reflectionElement = $this->createMock(ElementReflectionInterface::class);
        $reflectionElement->method('hasAnnotation')
            ->willReturnCallback(function ($arg) {
                return $arg === 'todo';
            });

        $this->assertCount(1, $this->elementFilter->filterByAnnotation([$reflectionElement], 'todo'));
        $this->assertCount(0, $this->elementFilter->filterByAnnotation([$reflectionElement], 'deprecated'));
    }
}
