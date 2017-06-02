<?php declare(strict_types=1);

namespace ApiGen\Tests\Utils;

use ApiGen\Utils\FileSystem;
use PHPUnit\Framework\TestCase;

final class FileSystemTest extends TestCase
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

    public function testEnsureDirectoryExists(): void
    {
        $filePath = TEMP_DIR . '/some/dir/file.txt';
        $dirPath = dirname($filePath);
        $this->assertFileNotExists($dirPath);

        FileSystem::ensureDirectoryExists($filePath);
        $this->assertFileExists($dirPath);
    }

    public function testPurgeDir(): void
    {
        $dir = TEMP_DIR . '/dir-with-content';
        mkdir($dir);
        mkdir($dir . '/dir-inside');
        file_put_contents($dir . '/file.txt', '...');

        @rmdir($dir);
        $this->assertFileExists($dir);

        $this->fileSystem->purgeDir($dir);
        $this->assertFileExists($dir);

        rmdir($dir);
        $this->assertFileNotExists($dir);
    }

    public function testPurgeDirOnNonExistingDir(): void
    {
        $dir = TEMP_DIR . '/not-created-dir';
        $this->assertFileNotExists($dir);

        $this->fileSystem->purgeDir($dir);
        $this->assertFileExists($dir);
    }

    public function testGetAbsolutePath(): void
    {
        $absoluteDir = $this->fileSystem->normalizePath(TEMP_DIR . '/relative-dir');
        mkdir($absoluteDir);
        $this->assertFileExists($absoluteDir);

        $absoluteFile = $absoluteDir . DIRECTORY_SEPARATOR . 'file.txt';
        file_put_contents($absoluteFile, '...');
        $this->assertFileExists($absoluteFile);

        $this->assertSame($absoluteDir, $this->fileSystem->getAbsolutePath($absoluteDir));

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
