<?php declare(strict_types=1);

namespace ApiGen\Utils\Tests\FileSystem;

use ApiGen\Utils\FileSystem;
use PHPUnit\Framework\TestCase;

final class FileSystemCopyTest extends TestCase
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
            [__DIR__ . '/FileSystemCopySource/SomeDir/someFile.txt' => 'renamedFile.txt'],
            TEMP_DIR . '/destination'
        );
        $this->assertFileExists(TEMP_DIR . '/destination');
        $this->assertFileExists(TEMP_DIR . '/destination/renamedFile.txt');
    }

    public function testCopyDirectory(): void
    {
        $this->fileSystem->copy([__DIR__ . '/FileSystemCopySource/SomeDir' => 'NewDir'], TEMP_DIR . '/destination');
        $this->assertFileExists(TEMP_DIR . '/destination/NewDir');
        $this->assertFileExists(TEMP_DIR . '/destination/NewDir/someFile.txt');
    }
}
