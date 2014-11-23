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
use Nette\Utils\Finder;
use RecursiveDirectoryIterator as RDI;
use RuntimeException;
use SplFileInfo;


/**
 * @method array    getSymlinks()
 * @method Scanner  onScanFinish(Scanner $scanner)
 */
class Scanner extends Nette\Object
{

	/**
	 * @var array
	 */
	public $onScanFinish = [];

	/**
	 * @var array
	 */
	private $symlinks = [];


	/**
	 * @param array $sources
	 * @param array $exclude
	 * @param array $extensions
	 * @throws RuntimeException
	 * @return SplFileInfo[]
	 */
	public function scan(array $sources, array $exclude = [], array $extensions = ['php'])
	{
		$fileMasks = $this->turnExtensionsToMask($extensions);
		$sources = $this->extractPharSources($sources);
		$finder = Finder::findFiles($fileMasks)->exclude($exclude)
			->from($sources)->exclude($exclude);
		$files = $this->convertFinderToArray($finder);

		if (count($files) === 0) {
			throw new RuntimeException('No PHP files found');
		}

		$this->symlinks = $this->getSymlinksFromFiles($files);
		$this->onScanFinish($this);

		return $files;
	}


	/**
	 * @return array
	 */
	private function turnExtensionsToMask(array $extensions)
	{
		array_walk($extensions, function (&$value) {
			$value = '*.' . $value;
		});
		return $extensions;
	}


	/**
	 * @param SplFileInfo[] $files
	 * @return array
	 */
	private function getSymlinksFromFiles(array $files)
	{
		$symlinks = [];
		foreach ($files as $file) {
			$pathName = FileSystem::normalizePath($file->getPathName());
			$files[$pathName] = $file->getSize();
			if ($file->getRealPath() !== FALSE && $file->getRealPath() !== $pathName) {
				$symlinks[$file->getRealPath()] = $pathName;
			}
		}
		return $symlinks;
	}


	/**
	 * @return array
	 */
	private function extractPharSources(array $sources)
	{
		foreach ($sources as $i => $source) {
			if (FileSystem::isPhar($source)) {
				if ( ! extension_loaded('phar')) {
					throw new RuntimeException('Phar extension is not loaded');
				}

				$dir = sys_get_temp_dir() . '/_apigen_temp/phar_' . $i;
				$phar = new \Phar($source, RDI::CURRENT_AS_FILEINFO | RDI::SKIP_DOTS | RDI::FOLLOW_SYMLINKS);
				$phar->extractTo($dir, NULL, TRUE);
				$sources[$i] = $dir;
			}
		}
		return $sources;
	}


	/**
	 * @return SplFileInfo[]
	 */
	private function convertFinderToArray(Finder $finder)
	{
		return iterator_to_array($finder->getIterator());
	}

}
