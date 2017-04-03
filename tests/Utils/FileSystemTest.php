<?php declare(strict_types=1);

namespace ApiGen\Utils\Tests\FileSystem;

use ApiGen\Utils\FileSystem;
use PHPUnit\Framework\TestCase;

class FileSystemTest extends TestCase
{
    /**
     * @var FileSystem
     */
    private $fileSystem;

    protected function setUp(): void
    {
        $this->fileSystem = new FileSystem;
    }

    public function testNormalizePath(): void
    {
        $backslashPath = 'C:' . DIRECTORY_SEPARATOR . 'Program Files' . DIRECTORY_SEPARATOR . 'ApiGen';
        $this->assertSame($backslashPath, $this->fileSystem->normalizePath($backslashPath));
    }

    public function testForceDir(): void
    {
        $filePath = TEMP_DIR . '/some/dir/file.txt';
        $dirPath = dirname($filePath);
        $this->assertFalse(file_exists($dirPath));

        $this->fileSystem->forceDir($filePath);
        $this->assertTrue(file_exists($dirPath));
    }

    public function testDeleteDir(): void
    {
        $dir = TEMP_DIR . '/new-dir';
        mkdir($dir);
        $this->assertTrue(file_exists($dir));

        $this->fileSystem->deleteDir($dir);
        $this->assertFalse(file_exists($dir));
    }

    public function testPurgeDir(): void
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

    public function testPurgeDirOnNonExistingDir(): void
    {
        $dir = TEMP_DIR . '/not-created-dir';
        $this->assertFalse(file_exists($dir));

        $this->fileSystem->purgeDir($dir);
        $this->assertTrue(file_exists($dir));
    }

    public function testGetAbsolutePath(): void
    {
        $absoluteDir = $this->fileSystem->normalizePath(TEMP_DIR . '/relative-dir');
        mkdir($absoluteDir);
        $this->assertTrue(file_exists($absoluteDir));

        $absoluteFile = $absoluteDir . DIRECTORY_SEPARATOR . 'file.txt';
        file_put_contents($absoluteFile, '...');
        $this->assertTrue(file_exists($absoluteFile));

        $this->assertSame($absoluteDir, $this->fileSystem->getAbsolutePath($absoluteDir));
        $this->assertSame(
            $absoluteDir . DIRECTORY_SEPARATOR . 'file.txt',
            $this->fileSystem->getAbsolutePath('file.txt', [$absoluteDir]));

        $this->assertSame(
            'someFile.txt',
            $this->fileSystem->getAbsolutePath('someFile.txt')
        );

        $testFile = DIRECTORY_SEPARATOR . 'someDir' . DIRECTORY_SEPARATOR . 'someDeeperFile.txt';
        $this->assertSame(
            $testFile,
            $this->fileSystem->getAbsolutePath($testFile)
        );
    }

    public function testIsDirEmpty(): void
    {
        $this->assertTrue($this->fileSystem->isDirEmpty(__DIR__ . '/FileSystemSource/EmptyDir'));
        $this->assertFalse($this->fileSystem->isDirEmpty(__DIR__ . '/FileSystemSource/NonEmptyDir'));
    }
}
