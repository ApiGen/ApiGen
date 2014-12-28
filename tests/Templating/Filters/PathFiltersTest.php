<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\PathFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class PathFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var PathFilters
	 */
	private $pathFilters;


	protected function setUp()
	{
		$relativePathResolverMock = Mockery::mock('ApiGen\Generator\Resolvers\RelativePathResolver');
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
