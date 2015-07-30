<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Theme;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\Utils\FileSystem;
use Nette\Utils\Finder;
use RecursiveDirectoryIterator;
use SplFileInfo;

class ThemeResources
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var FileSystem
     */
    private $fileSystem;


    public function __construct(Configuration $configuration, FileSystem $fileSystem)
    {
        $this->configuration = $configuration;
        $this->fileSystem = $fileSystem;
    }


    /**
     * @param string $destination
     */
    public function copyToDestination($destination)
    {
        $resources = $this->configuration->getOption(CO::TEMPLATE)['resources'];
        foreach ($resources as $resourceSource => $resourceDestination) {
            // File
            if (is_file($resourceSource)) {
                copy($resourceSource, $this->fileSystem->forceDir($destination  . '/' . $resourceDestination));
                continue;
            }

            // Dir
            /** @var RecursiveDirectoryIterator $iterator */
            $iterator = Finder::findFiles('*')->from($resourceSource)->getIterator();
            foreach ($iterator as $item) {
                /** @var SplFileInfo $item */
                copy($item->getPathName(), $this->fileSystem->forceDir($destination
                    . '/' . $resourceDestination
                    . '/' . $iterator->getSubPathName()));
            }
        }
    }
}
