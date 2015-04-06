<?php

namespace ApiGen\Tests\Theme;

use ApiGen\Theme\ThemeResources;
use ApiGen\Utils\FileSystem;
use Mockery;
use PHPUnit_Framework_TestCase;


class ThemeResourcesTest extends PHPUnit_Framework_TestCase
{

	public function testCopyToDestination()
	{
		$sourceDir = TEMP_DIR . '/source';
		$sourceFile = TEMP_DIR . '/other-source/other-file.txt';
		$destinationDir = TEMP_DIR . '/destination';

		$configurationMock = Mockery::mock('ApiGen\Configuration\Configuration');
		$configurationMock->shouldReceive('getOption')->with('template')->andReturn([
			'resources' => [
				$sourceFile => 'other-file-renamed.txt',
				$sourceDir => 'assets'
			]
		]);
		$this->prepareSources($sourceFile, $sourceDir);

		$themeResources = new ThemeResources($configurationMock, new FileSystem);
		$themeResources->copyToDestination($destinationDir);

		$this->assertFileExists($destinationDir . '/assets/file.txt');
		$this->assertFileExists($destinationDir . '/other-file-renamed.txt');
	}


	/**
	 * @param string $sourceFile
	 * @param string $sourceDir
	 */
	private function prepareSources($sourceFile, $sourceDir)
	{
		mkdir(dirname($sourceFile), 0777);
		file_put_contents($sourceFile, '...');
		mkdir($sourceDir, 0777);
		file_put_contents($sourceDir . '/file.txt', '...');
	}

}
