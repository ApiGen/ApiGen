<?php declare(strict_types=1);

namespace ApiGen\Utils;

use Nette\Utils\FileSystem as NetteFileSystem;
use Nette\Utils\Finder;
use RecursiveDirectoryIterator;
use SplFileInfo;

final class FileSystem
{
    public function normalizePath(string $path): string
    {
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }

    public function forceDir(string $path): string
    {
        @mkdir($path, 0755, true);
        $directory = dirname($path);
        @mkdir($directory, 0755, true);
        return $path;
    }

    public function deleteDir(string $path): void
    {
        NetteFileSystem::delete($path);
    }

    public function purgeDir(string $path): void
    {
        NetteFileSystem::delete($path);
        NetteFileSystem::createDir($path);
    }

    /**
     * @param string $path
     * @param string[] $baseDirectories
     * @return string
     */
    public function getAbsolutePath(string $path, array $baseDirectories = []): string
    {
        foreach ($baseDirectories as $directory) {
            $fileName = $directory . '/' . $path;
            if (is_file($fileName)) {
                return $this->normalizePath(realpath($fileName));
            }
        }

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

    /**
     * @param string[]|string[][] $source
     * @param string $destination
     */
    public function copy(array $source, string $destination): void
    {
        foreach ($source as $resourceSource => $resourceDestination) {
            if (is_file($resourceSource)) {
                copy($resourceSource, FileSystem::forceDir($destination  . '/' . $resourceDestination));
            }

            if (is_dir($resourceSource)) {
                /** @var RecursiveDirectoryIterator $iterator */
                $iterator = Finder::findFiles('*')->from($resourceSource)->getIterator();
                foreach ($iterator as $item) {
                    /** @var SplFileInfo $item */
                    copy($item->getPathname(), FileSystem::forceDir($destination
                        . '/' . $resourceDestination
                        . '/' . $iterator->getSubPathname()));
                }
            }
        }
    }

    public function copyDirectory(string $sourceDirectory, string $destinationDirectory): void
    {
        FileSystem::forceDir($destinationDirectory);

        /** @var RecursiveDirectoryIterator $iterator */
        $fileInfos = Finder::findFiles('*')->from($sourceDirectory)
            ->getIterator();

        foreach ($fileInfos as $fileInfo) {
            $sourceFile = $fileInfo->getPathname();
            $destinationFile = $destinationDirectory . DIRECTORY_SEPARATOR . $fileInfo->getFilename();

            copy($sourceFile, $destinationFile);
        }
    }

    public static function ensureDirectoryExists(string $destination): void
    {
        $directory = dirname($destination);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }
}
