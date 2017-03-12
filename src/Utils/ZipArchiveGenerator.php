<?php

namespace ApiGen\Utils;

use Nette\Utils\Finder;
use Nette\Utils\Strings;
use SplFileInfo;
use ZipArchive;

class ZipArchiveGenerator
{

    /**
     * @param string $source
     * @param string $zipFile
     */
    public function zipDirToFile($source, $zipFile)
    {
        $archive = new ZipArchive;
        $archive->open($zipFile, ZipArchive::CREATE);

        $directory = basename($zipFile, '.zip');

        /** @var SplFileInfo $file */
        foreach (Finder::findFiles('*')->from($source) as $file) {
            $relativePath = Strings::substring($file->getRealPath(), strlen($source) + 1);
            $archive->addFile($file, $directory . '/' . $relativePath);
        }

        $archive->close();
    }
}
