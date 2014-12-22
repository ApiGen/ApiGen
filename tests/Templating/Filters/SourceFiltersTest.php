<?php

namespace ApiGen\Tests\Templating\Filters;

use ApiGen\Templating\Filters\SourceFilters;
use Mockery;
use PHPUnit_Framework_TestCase;


class SourceFiltersTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var SourceFilters
	 */
	private $sourceFilters;


	protected function setUp()
	{
		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with('destination')->andReturn(TEMP_DIR);
		$this->sourceFilters = new SourceFilters($configurationMock);
	}


	public function testStaticFile()
	{
		file_put_contents(TEMP_DIR . '/some-file.txt', '...');
		$link = $this->sourceFilters->staticFile('some-file.txt');
		$this->assertSame('some-file.txt?6eae3a5b062c6d0d79f070c26e6d62486b40cb46', $link);
	}

}
