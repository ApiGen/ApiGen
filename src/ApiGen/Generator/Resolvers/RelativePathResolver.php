<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\FileSystem\FileSystem;
use Nette;


/**
 * Resolves relative path to elements extracted by Generator.
 *
 * @method  RelativePathResolver setSymlinks(array $symlinks)
 * @method  RelativePathResolver setConfig(array $config)
 */
class RelativePathResolver extends Nette\Object
{

	/**
	 * @var array
	 */
	private $symlinks;

	/**
	 * @var array
	 */
	private $config;



	/**
	 * Returns filename relative path to the source directory.
	 *
	 * @param string $fileName
	 * @throws \InvalidArgumentException
	 * @return string
	 */
	public function getRelativePath($fileName)
	{
		if (isset($this->symlinks[$fileName])) {
			$fileName = $this->symlinks[$fileName];
		}
		foreach ($this->config['source'] as $source) {
			if (FileSystem::isPhar($source)) {
				$source = FileSystem::pharPath($source);
			}
			if (strpos($fileName, $source) === 0) {
				return is_dir($source) ? str_replace('\\', '/', substr($fileName, strlen($source) + 1)) : basename($fileName);
			}
		}

		throw new \InvalidArgumentException(sprintf('Could not determine "%s" relative path', $fileName));
	}
}
