<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Utils;

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
        self::purgeDir($path);
        rmdir($path);
    }


    /**
     * @param string $path
     */
    public function purgeDir($path)
    {
        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        foreach (Finder::find('*')->from($path)->childFirst() as $item) {
            /** @var \SplFileInfo $item */
            if ($item->isDir()) {
                rmdir($item);

            } else {
                unlink($item);
            }
        }
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
                return self::normalizePath(realpath($fileName));
            }
        }

        if (file_exists($path)) {
            $path = realpath($path);
        }

        return self::normalizePath($path);
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
