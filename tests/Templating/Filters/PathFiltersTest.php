<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Generator\Resolvers\RelativePathResolver;
use ApiGen\Templating\Filters\PathFilters;
use Mockery;
use PHPUnit\Framework\TestCase;

class PathFiltersTest extends TestCase
{

    /**
     * @var PathFilters
     */
    private $pathFilters;


    protected function setUp()
    {
        $relativePathResolverMock = Mockery::mock(RelativePathResolver::class);
        $relativePathResolverMock->shouldReceive('getRelativePath')->andReturnUsing(function ($arg) {
            return '../' . $arg;
        });
        $this->pathFilters = new PathFilters($relativePathResolverMock);
    }


    public function testRelativePath()
    {
        $this->assertSame('../someFile.txt', $this->pathFilters->relativePath('someFile.txt'));
    }
}
