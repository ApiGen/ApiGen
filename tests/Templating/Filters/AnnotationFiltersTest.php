<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\AnnotationFilterProvider;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class AnnotationFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var AnnotationFilterProvider
     */
    private $annotationFilters;

    protected function setUp(): void
    {
        $this->annotationFilters = $this->container->getByType(AnnotationFilterProvider::class);
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
