<?php

namespace ApiGen\Tests\Theme;

use ApiGen\Configuration\Configuration;
use ApiGen\Theme\ThemeResources;
use Mockery;
use PHPUnit_Framework_TestCase;


class ThemeResourcesTest extends PHPUnit_Framework_TestCase
{

	public function testCopyToDestination()
	{
		$sourceDir = TEMP_DIR . '/source';
		$sourceFile = TEMP_DIR . '/other-source/other-file.txt';
		$destinationDir = TEMP_DIR . '/destination';

		$configurationMock = Mockery::mock(Configuration::class);
		$configurationMock->shouldReceive('getOption')->with('template')->andReturn([
			'resources' => [
				$sourceFile => 'other-file-renamed.txt',
				$sourceDir => 'assets'
			]
		]);
		$this->prepareSources($sourceFile, $sourceDir);

		$themeResources = new ThemeResources($configurationMock);
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
