<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\FileSystem;

use Nette;


class Wiper extends Nette\Object
{

	/**
	 * @var Finder
	 */
	private $finder;

	/**
	 * @var ZipArchiveGenerator
	 */
	private $zip;


	public function __construct(Finder $finder, ZipArchiveGenerator $zip)
	{
		$this->finder = $finder;
		$this->zip = $zip;
	}


	/**
	 * Wipes out the destination directory.
	 */
	public function wipOutDestination()
	{
		foreach ($this->finder->findGeneratedFiles() as $path) {
			if (is_file($path) && ! @unlink($path)) {
				throw new \Exception('Cannot wipe out destination directory');
			}
		}

		$archive = $this->zip->getArchivePath();
		if (is_file($archive) && ! @unlink($archive)) {
			throw new \Exception('Cannot wipe out destination directory');
		}
	}

}
