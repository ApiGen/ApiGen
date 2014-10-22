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
use Tester\Helpers;


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
	 * @param array $sources List of sources to be scanned (folder or files).
	 * @param array $exclude Masks for folders/files to be excluded.
	 * @param array $extensions File extensions to be scanned (e.g. php, phpt).
	 * @throws RuntimeException
	 * @return Finder
	 */
	public function scan(array $sources, array $exclude = array(), array $extensions = array('php'))
	{
		$sources = $this->extractPharSources($sources);
		$fileMasks = $this->turnExtensionsToMask($extensions);
		$files = Finder::findFiles($fileMasks)->exclude($exclude)
			->from($sources)->exclude($exclude);

		if (iterator_count($files) === 0) {
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
	 * @return array
	 */
	private function getSymlinksFromFiles(Finder $finder)
	{
		$symlinks = array();
		foreach ($finder->getIterator() as $file) {
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

				$dir = sys_get_temp_dir() . DS . '_apigen_temp' . DS . 'phar_' . $i;
				$phar = new \Phar($source, RDI::CURRENT_AS_FILEINFO | RDI::SKIP_DOTS | RDI::FOLLOW_SYMLINKS);
				$phar->extractTo($dir, NULL, TRUE);
				$sources[$i] = $dir;
			}
		}
		return $sources;
	}

}
