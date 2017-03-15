<?php declare(strict_types=1);

namespace ApiGen\Utils\Tests\Neon\NeonFile;

use ApiGen\Utils\FileSystem;
use PHPUnit\Framework\TestCase;

class CopyTest extends TestCase
{

    /**
     * @var FileSystem
     */
    private $fileSystem;


    protected function setUp(): void
    {
        $this->fileSystem = new FileSystem;
    }


    public function testCopyFiles(): void
    {
        $this->fileSystem->copy(
            [__DIR__ . '/CopySource/SomeDir/someFile.txt' => 'renamedFile.txt'],
            TEMP_DIR . '/destination'
        );
        $this->assertFileExists(TEMP_DIR . '/destination');
        $this->assertFileExists(TEMP_DIR . '/destination/renamedFile.txt');
    }


    public function testCopyDirectory(): void
    {
        $this->fileSystem->copy([__DIR__ . '/CopySource/SomeDir' => 'NewDir'], TEMP_DIR . '/destination');
        $this->assertFileExists(TEMP_DIR . '/destination/NewDir');
        $this->assertFileExists(TEMP_DIR . '/destination/NewDir/someFile.txt');
    }
}
