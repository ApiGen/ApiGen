<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\Configuration\ConfigurationOptions as CO;
use ApiGen\FileSystem\FileSystem;
use InvalidArgumentException;
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
	 * @throws InvalidArgumentException
	 * @return string
	 */
	public function getRelativePath($fileName)
	{
        $fileName = str_replace('\\', '/', $fileName);
		if (isset($this->symlinks[$fileName])) {
			$fileName = $this->symlinks[$fileName];
		}
		foreach ($this->config[CO::SOURCE] as $source) {
			if (strpos($fileName, $source) === 0) {
				return $this->getFileNameWithoutSourcePath($fileName, $source);
			}
		}

		throw new InvalidArgumentException(sprintf('Could not determine "%s" relative path', $fileName));
	}


	/**
	 * @param string $fileName
	 * @param string $source
	 * @return string
	 */
	private function getFileNameWithoutSourcePath($fileName, $source)
	{
		$source = rtrim($source, '/');
		$fileName = substr($fileName, strlen($source) + 1);
		return str_replace('\\', '/', $fileName);
	}

}
