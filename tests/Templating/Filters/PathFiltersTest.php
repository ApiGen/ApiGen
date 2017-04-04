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
        $testPath = 'Templating' . DIRECTORY_SEPARATOR . 'Filters'
            . DIRECTORY_SEPARATOR . 'FiltersSource'
            . DIRECTORY_SEPARATOR . 'FooFilters.php';

        $this->assertSame(
            $testPath,
            $this->pathFilters->relativePath(
                __DIR__ . DIRECTORY_SEPARATOR . 'FiltersSource' . DIRECTORY_SEPARATOR . 'FooFilters.php')
        );
    }
}
