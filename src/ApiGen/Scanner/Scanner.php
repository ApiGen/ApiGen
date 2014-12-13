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
	 * @return SplFileInfo[]
	 */
	private function convertFinderToArray(Finder $finder)
	{
		return iterator_to_array($finder->getIterator());
	}

}
