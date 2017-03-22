<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\PathFilters;
use ApiGen\Tests\AbstractContainerAwareTestCase;

final class PathFiltersTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PathFilters
     */
    private $pathFilters;

    protected function setUp(): void
    {
        $this->pathFilters = $this->container->getByType(PathFilters::class);
    }

    public function testRelativePath(): void
    {
        $this->assertSame(
            'Templating/Filters/FiltersSource/FooFilters.php',
            $this->pathFilters->relativePath(__DIR__ . '/FiltersSource/FooFilters.php')
        );
    }
}
