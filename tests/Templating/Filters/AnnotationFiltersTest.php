<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\AnnotationFilters;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class AnnotationFiltersTest extends TestCase
{
    /**
     * @var AnnotationFilters
     */
    private $annotationFilters;

    protected function setUp(): void
    {
        $this->annotationFilters = new AnnotationFilters(new EventDispatcher);
    }

    public function testAnnotationFilterWithCustom(): void
    {
        $annotations = ['remain' => true, 'otherToRemain' => true];
        $this->assertSame(
            ['otherToRemain' => true],
            $this->annotationFilters->annotationFilter($annotations, ['remain'])
        );
    }
}
