<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\Configuration\ConfigurationOptions as CO;
use InvalidArgumentException;


class RelativePathResolver
{

	/**
	 * @var array
	 */
	private $symlinks;

	/**
	 * @var array
	 */
	private $config;


	public function setConfig(array $config)
	{
		$this->config = $config;
	}


	public function setSymlinks(array $symlinks)
	{
		$this->symlinks = $symlinks;
	}


	/**
	 * Returns filename relative path to the source directory.
	 *
	 * @param string $fileName
	 * @return string
	 */
	public function getRelativePath($fileName)
	{
		$fileName = $this->uniteSlashes($fileName);
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
		return $this->uniteSlashes($fileName);
	}


	/**
	 * @param string $path
	 * @return string
	 */
	private function uniteSlashes($path)
	{
		return str_replace('\\', '/', $path);
	}

}
