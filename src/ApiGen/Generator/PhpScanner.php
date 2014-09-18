<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator;

use ApiGen\FileSystem;
use ApiGen\SourceFilesFilterIterator;
use Nette;
use RecursiveDirectoryIterator as RDI;
use RuntimeException;


/**
 * @method array getSymlinks()
 */
class PhpScanner extends Nette\Object implements Scanner
{

	/**
	 * @var array
	 */
	private $symlinks = array();


	/**
	 * @param array $sources
	 * @param array $exclude
	 * @param array $extensions
	 * @throws RuntimeException
	 * @return array
	 */
	public function scan($sources, $exclude = array(), $extensions = array())
	{
		$files = array();
		$flags = RDI::CURRENT_AS_FILEINFO | RDI::SKIP_DOTS | RDI::FOLLOW_SYMLINKS;

		foreach ($sources as $source) {
			$entries = array();

			if (is_dir($source)) {
				$directoryIterator = new RDI($source, $flags);
				$sourceFilesFilterIterator = new SourceFilesFilterIterator($directoryIterator, $exclude);
				foreach (new \RecursiveIteratorIterator($sourceFilesFilterIterator) as $entry) {
					if ( ! $entry->isFile()) {
						continue;
					}
					$entries[] = $entry;
				}

			} elseif (FileSystem::isPhar($source)) {
				if ( ! extension_loaded('phar')) {
					throw new RuntimeException('Phar extension is not loaded');
				}
				foreach (new \RecursiveIteratorIterator(new \Phar($source, $flags)) as $entry) {
					if ( ! $entry->isFile()) {
						continue;
					}
					$entries[] = $entry;
				}

			} else {
				$entries[] = new \SplFileInfo($source);
			}

			$regexp = '~\\.' . implode('|', $extensions) . '$~i';

			/** @var \SplFileInfo $entry */
			foreach ($entries as $entry) {
				if ( ! preg_match($regexp, $entry->getFilename())) {
					continue;
				}

				$pathName = FileSystem::normalizePath($entry->getPathName());
				$files[$pathName] = $entry->getSize();
				if ($entry->getRealPath() !== FALSE && $entry->getRealPath() !== $pathName) {
					$this->symlinks[$entry->getRealPath()] = $pathName;
				}
			}
		}

		if (empty($files)) {
			throw new RuntimeException('No PHP files found');
		}

		return $files;
	}

}
