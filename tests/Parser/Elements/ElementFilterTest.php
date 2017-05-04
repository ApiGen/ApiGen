<?php declare(strict_types=1);

namespace ApiGen\Tests\Parser\Elements;

use ApiGen\Contracts\Parser\Reflection\ReflectionInterface;
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
        $reflectionElement = $this->createMock(ReflectionInterface::class);
        $reflectionElement->method('hasAnnotation')
            ->willReturnCallback(function ($arg) {
                return $arg === 'todo';
            });

        $this->assertCount(1, $this->elementFilter->filterByAnnotation([$reflectionElement], 'todo'));
        $this->assertCount(0, $this->elementFilter->filterByAnnotation([$reflectionElement], 'deprecated'));
    }
}
