<?php declare(strict_types=1);

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Templating\Filters\PathFilters;
use PHPUnit\Framework\TestCase;

class PathFiltersTest extends TestCase
{

    /**
     * @var PathFilters
     */
    private $pathFilters;


    protected function setUp(): void
    {
        $relativePathResolverMock = $this->createMock(RelativePathResolver::class);
        $relativePathResolverMock->method('getRelativePath')->willReturnCallback(function ($arg) {
            return '../' . $arg;
        });
        $this->pathFilters = new PathFilters($relativePathResolverMock);
    }


    public function testRelativePath(): void
    {
        $this->assertSame('../someFile.txt', $this->pathFilters->relativePath('someFile.txt'));
    }
}
