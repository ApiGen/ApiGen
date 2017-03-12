<?php

namespace ApiGen\Utils\Tests\Neon\NeonFile;

use ApiGen\Utils\FileSystem;
use PHPUnit\Framework\TestCase;

class CopyTest extends TestCase
{

    /**
     * @var FileSystem
     */
    private $fileSystem;


    protected function setUp()
    {
        $this->fileSystem = new FileSystem;
    }


    public function testCopyFiles()
    {
        $this->fileSystem->copy(
            [__DIR__ . '/CopySource/SomeDir/someFile.txt' => 'renamedFile.txt'],
            TEMP_DIR . '/destination'
        );
        $this->assertFileExists(TEMP_DIR . '/destination');
        $this->assertFileExists(TEMP_DIR . '/destination/renamedFile.txt');
    }


    public function testCopyDirectory()
    {
        $this->fileSystem->copy([__DIR__ . '/CopySource/SomeDir' => 'NewDir'], TEMP_DIR . '/destination');
        $this->assertFileExists(TEMP_DIR . '/destination/NewDir');
        $this->assertFileExists(TEMP_DIR . '/destination/NewDir/someFile.txt');
    }
}
