<?php

namespace ApiGen\Utils\Tests;

use ApiGen\Utils\ZipArchiveGenerator;
use PHPUnit\Framework\TestCase;

class ZipArchiveGeneratorTest extends TestCase
{

    public function testZipDirToFile()
    {
        $dirPath = TEMP_DIR . '/some-dir-to-zip';
        mkdir($dirPath, 0777, true);
        file_put_contents($dirPath . '/file.txt', 'file content');

        $archiveName = $dirPath . '/API-archive.zip';
        (new ZipArchiveGenerator)->zipDirToFile($dirPath, $archiveName);
        $this->assertFileExists($archiveName);
    }
}
