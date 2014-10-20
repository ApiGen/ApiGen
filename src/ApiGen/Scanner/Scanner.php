<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Scanner;

use ApiGen\FileSystem\FileSystem;
use Nette;
use RecursiveDirectoryIterator as RDI;
use RecursiveIterator;
use RecursiveIteratorIterator;
use RuntimeException;


/**
 * @method Scanner  onScanFinish(Scanner $scanner)
 * @method array    getSymlinks()
 */
class Scanner extends Nette\Object
{

	/**
	 * @var array
	 */
	public $onScanFinish = array();

	/**
	 * @var array
	 */
	private $symlinks = array();


	/**
	 * Scans sources and return found files
	 *
	 * @param array $sources List of sources to be scanned (folder or files).
	 * @param array $exclude Excluded files.
	 * @param array $extensions File extensions to be scanned (e.g. php, phpt).
	 * @throws RuntimeException
	 * @return array
	 */
	public function scan($sources, $exclude = array(), $extensions = array('php'))
	{
		$files = array();

		foreach ($sources as $source) {
			$entries = $this->getEntriesFromSource($source, $exclude);

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

		$this->onScanFinish($this);

		return $files;
	}


	/**
	 * @param string $source
	 * @param array $exclude
	 * @return array
	 */
	private function getEntriesFromSource($source, $exclude = array())
	{
		if (is_dir($source)) {
			$directoryIterator = new RDI($source, $this->getIteratorFlags());
			$sourceFilesFilterIterator = new SourceFilesFilterIterator($directoryIterator, $exclude);
			return $this->getFilesFromIterator($sourceFilesFilterIterator);

		} elseif (FileSystem::isPhar($source)) {
			if ( ! extension_loaded('phar')) {
				throw new RuntimeException('Phar extension is not loaded');
			}
			$phar = new \Phar($source, $this->getIteratorFlags());
			return $this->getFilesFromIterator($phar);

		} else {
			return array(new \SplFileInfo($source));
		}
	}


	/**
	 * @return int
	 */
	private function getIteratorFlags()
	{
		return RDI::CURRENT_AS_FILEINFO | RDI::SKIP_DOTS | RDI::FOLLOW_SYMLINKS;
	}


	/**
	 * @return array
	 */
	private function getFilesFromIterator(RecursiveIterator $iterator)
	{
		$files = array();
		foreach (new RecursiveIteratorIterator($iterator) as $entry) {
			if ( ! $entry->isFile()) {
				continue;
			}
			$files[] = $entry;
		}
		return $files;
	}

}
