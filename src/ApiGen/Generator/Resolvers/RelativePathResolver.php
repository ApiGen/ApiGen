<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\Configuration\Configuration;
use ApiGen\FileSystem\FileSystem;
use Nette;


/**
 * Resolves relative path to elements extracted by Generator.
 *
 * @method setSymlinks()
 */
class RelativePathResolver extends Nette\Object
{

	/**
	 * @var array
	 */
	private $symlinks;

	/**
	 * @var Configuration
	 */
	private $configuration;


	public function __construct(Configuration $configuration)
	{
		$this->configuration = $configuration;
	}


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

		$options = $this->configuration->getOptions();
		foreach ($options['source'] as $source) {
			if (FileSystem::isPhar($source)) {
				$source = FileSystem::pharPath($source);
			}

			if (strpos($fileName, $source) === 0) {
				return $this->getFileNameWithoutSourcePath($fileName, $source);
			}
		}

		throw new \InvalidArgumentException(sprintf('Could not determine "%s" relative path', $fileName));
	}


	/**
	 * @param string $fileName
	 * @param string $source
	 * @return string
	 */
	private function getFileNameWithoutSourcePath($fileName, $source)
	{
		$source = rtrim($source, DS);
		$fileName = substr($fileName, strlen($source) + 1);
		return str_replace('\\', DS, $fileName);
	}

}
