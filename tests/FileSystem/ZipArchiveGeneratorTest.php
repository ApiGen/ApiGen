<?php

namespace ApiGen\Tests\FileSystem;

use ApiGen\FileSystem\ZipArchiveGenerator;
use ApiGen\Tests\ContainerAwareTestCase;


class ZipArchiveGeneratorTest extends ContainerAwareTestCase
{

	/**
	 * @var ZipArchiveGenerator
	 */
	private $zipArchiveGenerator;


	protected function setUp()
	{
		$this->zipArchiveGenerator = $this->container->getByType('ApiGen\FileSystem\ZipArchiveGenerator');
		if ( ! file_exists($this->destinationDir)) {
			mkdir($this->destinationDir);
		}
	}


	public function testZipDirToFile()
	{
		file_put_contents($this->destinationDir . '/file.txt', 'file content');
		$archiveName = $this->destinationDir . '/API-archive.zip';
		$this->zipArchiveGenerator->zipDirToFile($this->destinationDir, $archiveName);
		$this->assertFileExists($archiveName);
	}

}
