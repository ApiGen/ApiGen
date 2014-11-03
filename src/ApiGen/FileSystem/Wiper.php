<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\FileSystem;

use Nette;
use Symfony\Component\Filesystem\Filesystem;


class Wiper extends Nette\Object
{

	/**
	 * @var Filesystem
	 */
	private $filesystem;


	public function __construct(Filesystem $filesystem)
	{
		$this->filesystem = $filesystem;
	}


	/**
	 * @param string $dir
	 */
	public function wipeOutDir($dir)
	{
		$this->filesystem->remove($dir);
		$this->filesystem->mkdir($dir);
	}

}
