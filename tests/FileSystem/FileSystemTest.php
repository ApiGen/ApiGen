<?php

namespace ApiGen\Tests\FileSystem;

use ApiGen\FileSystem\FileSystem;
use ApiGen\Tests\MethodInvoker;
use PHPUnit_Framework_TestCase;


class FileSystemTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var FileSystem
	 */
	private $fileSystem;


	protected function setUp()
	{
		$this->fileSystem = new FileSystem;
	}


	public function testNormalizePath()
	{
		$backslashPath = 'C:\User\Program File\ApiGen';
		$this->assertSame('C:/User/Program File/ApiGen', $this->fileSystem->normalizePath($backslashPath));
	}


	public function testForceDir()
	{
		$filePath = TEMP_DIR . '/some/dir/file.txt';
		$dirPath = dirname($filePath);
		$this->assertFalse(file_exists($dirPath));

		$this->fileSystem->forceDir($filePath);
		$this->assertTrue(file_exists($dirPath));
	}


	public function testDeleteDir()
	{
		$dir = TEMP_DIR . '/new-dir';
		mkdir($dir);
		$this->assertTrue(file_exists($dir));

		$this->fileSystem->deleteDir($dir);
		$this->assertFalse(file_exists($dir));
	}


	public function testPurgeDir()
	{
		$dir = TEMP_DIR . '/dir-with-content';
		mkdir($dir);
		mkdir($dir . '/dir-inside');
		file_put_contents($dir . '/file.txt', '...');

		@rmdir($dir);
		$this->assertTrue(file_exists($dir));

		$this->fileSystem->purgeDir($dir);
		$this->assertTrue(file_exists($dir));

		rmdir($dir);
		$this->assertFalse(file_exists($dir));
	}


	public function testPurgeDirOnNonExistingDir()
	{
		$dir = TEMP_DIR . '/not-created-dir';
		$this->assertFalse(file_exists($dir));

		$this->fileSystem->purgeDir($dir);
		$this->assertTrue(file_exists($dir));
	}


	public function testGetAbsolutePath()
	{
		$absoluteDir = TEMP_DIR . '/relative-dir';
		mkdir($absoluteDir);
		$this->assertTrue(file_exists($absoluteDir));

		$absoluteFile = $absoluteDir . '/file.txt';
		file_put_contents($absoluteFile, '...');
		$this->assertTrue(file_exists($absoluteFile));

		$this->assertSame($absoluteDir, $this->fileSystem->getAbsolutePath($absoluteDir));
		$this->assertSame($absoluteDir . '/file.txt', $this->fileSystem->getAbsolutePath('file.txt', [$absoluteDir]));
	}


	public function testIsAbsolutePath()
	{
		$this->assertFalse(MethodInvoker::callMethodOnObject($this->fileSystem, 'isAbsolutePath', ['file.txt']));
		$this->assertTrue(MethodInvoker::callMethodOnObject($this->fileSystem, 'isAbsolutePath', [TEMP_DIR]));
	}

}
