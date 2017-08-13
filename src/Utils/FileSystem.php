<?php declare(strict_types=1);

namespace ApiGen\Utils;

use Nette\Utils\FileSystem as NetteFileSystem;
use Nette\Utils\Finder;
use RecursiveDirectoryIterator;

final class FileSystem
{
    public function normalizePath(string $path): string
    {
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }

    public function purgeDir(string $path): void
    {
        NetteFileSystem::delete($path);
        NetteFileSystem::createDir($path);
    }

    public function getAbsolutePath(string $path): string
    {
        if (file_exists($path)) {
            $path = realpath($path);
        }

        if (file_exists(getcwd() . $path)) {
            $path = getcwd() . $path;
        }

        return $this->normalizePath($path);
    }

    public function isDirEmpty(string $path): bool
    {
        if (count(glob($path . '/*'))) {
            return false;
        }

        return true;
    }

    public function copyDirectory(string $sourceDirectory, string $destinationDirectory): void
    {
        self::ensureDirectoryExists($destinationDirectory);

        /** @var RecursiveDirectoryIterator $iterator */
        $fileInfos = Finder::findFiles('*')->from($sourceDirectory)
            ->getIterator();

        foreach ($fileInfos as $fileInfo) {
            $sourceFile = $fileInfo->getPathname();
            $destinationFile = $destinationDirectory . DIRECTORY_SEPARATOR . $fileInfo->getFilename();

            copy($sourceFile, $destinationFile);
        }
    }

    public static function ensureDirectoryExistsForFile(string $file): void
    {
        $directory = dirname($file);
        self::ensureDirectoryExists($directory);
    }

    public static function ensureDirectoryExists(string $directory): void
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
