<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\AnnotationFilters;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AnnotationFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AnnotationFilters
     */
    private $annotationFilters;

    protected function setUp(): void
    {
        $this->annotationFilters = $this->container->getByType(AnnotationFilters::class);
    }

    public function testAnnotationFilterWithCustom(): void
    {
        $annotationFilter = $this->annotationFilters->getFilters()['annotation'];

        $annotations = ['remain' => true, 'otherToRemain' => true];
        $this->assertSame(
            ['otherToRemain' => true],
            $annotationFilter($annotations, ['remain'])
        );
    }
}
