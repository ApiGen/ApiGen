<?php declare(strict_types=1);

namespace ApiGen\Utils;

use Nette\Utils\FileSystem as NetteFileSystem;
use Nette\Utils\Finder;

class FileSystem
{

    /**
     * @param string $path
     * @return string
     */
    public function normalizePath($path)
    {
        return str_replace('\\', '/', $path);
    }


    /**
     * @param string $path
     * @return string
     */
    public function forceDir($path)
    {
        @mkdir(dirname($path), 0755, true);
        return $path;
    }


    /**
     * @param string $path
     */
    public function deleteDir($path)
    {
        NetteFileSystem::delete($path);
    }


    /**
     * @param string $path
     */
    public function purgeDir($path)
    {
        NetteFileSystem::delete($path);
        NetteFileSystem::createDir($path);
    }


    /**
     * @param string $path
     * @param array $baseDirectories
     * @return string
     */
    public function getAbsolutePath($path, array $baseDirectories = [])
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

        return $this->normalizePath($path);
    }


    /**
     * @param string $path
     * @return bool
     */
    public function isDirEmpty($path)
    {
        if (count(glob($path . '/*'))) {
            return false;
        }
        return true;
    }


    /**
     * @param array $source
     * @param string $destination
     */
    public function copy(array $source, $destination)
    {
        foreach ($source as $resourceSource => $resourceDestination) {
            if (is_file($resourceSource)) {
                copy($resourceSource, FileSystem::forceDir($destination  . '/' . $resourceDestination));
                continue;
            } else {
                /** @var \RecursiveDirectoryIterator $iterator */
                $iterator = Finder::findFiles('*')->from($resourceSource)->getIterator();
                foreach ($iterator as $item) {
                    /** @var \SplFileInfo $item */
                    copy($item->getPathName(), FileSystem::forceDir($destination
                        . '/' . $resourceDestination
                        . '/' . $iterator->getSubPathName()));
                }
            }
        }
    }
}
