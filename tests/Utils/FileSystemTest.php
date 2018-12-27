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
        $backslashPath = sprintf('C:%sProgram Files%sApiGen', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        $this->assertSame($backslashPath, $this->fileSystem->normalizePath($backslashPath));
    }

    public function testEnsureDirectoryExists(): void
    {
        $filePath = sprintf('%s/some/dir/file.txt', TEMP_DIR);
        $dirPath = dirname($filePath);
        $this->assertFileNotExists($dirPath);

        FileSystem::ensureDirectoryExists($filePath);
        $this->assertFileExists($dirPath);
    }

    public function testPurgeDir(): void
    {
        $dir = sprintf('%s/dir-with-content', TEMP_DIR);
        mkdir($dir);
        mkdir(sprintf('%s/dir-inside', $dir));
        file_put_contents(sprintf('%s/file.txt', $dir), '...');

        @rmdir($dir);
        $this->assertFileExists($dir);

        $this->fileSystem->purgeDir($dir);
        $this->assertFileExists($dir);

        rmdir($dir);
        $this->assertFileNotExists($dir);
    }

    public function testPurgeDirOnNonExistingDir(): void
    {
        $dir = sprintf('%s/not-created-dir', TEMP_DIR);
        $this->assertFileNotExists($dir);

        $this->fileSystem->purgeDir($dir);
        $this->assertFileExists($dir);
    }

    public function testGetAbsolutePath(): void
    {
        $absoluteDir = $this->fileSystem->normalizePath(sprintf('%s/relative-dir', TEMP_DIR));
        mkdir($absoluteDir);
        $this->assertFileExists($absoluteDir);

        $absoluteFile = sprintf('%s%sfile.txt', $absoluteDir, DIRECTORY_SEPARATOR);
        file_put_contents($absoluteFile, '...');
        $this->assertFileExists($absoluteFile);

        $this->assertSame($absoluteDir, $this->fileSystem->getAbsolutePath($absoluteDir));

        $this->assertSame('someFile.txt', $this->fileSystem->getAbsolutePath('someFile.txt'));

        $testFile = sprintf('%ssomeDir%ssomeDeeperFile.txt', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        $this->assertSame($testFile, $this->fileSystem->getAbsolutePath($testFile));
    }

    public function testIsDirEmpty(): void
    {
        $this->assertTrue($this->fileSystem->isDirEmpty(sprintf('%s/FileSystemSource/EmptyDir', __DIR__)));
        $this->assertFalse($this->fileSystem->isDirEmpty(sprintf('%s/FileSystemSource/NonEmptyDir', __DIR__)));
    }
}
