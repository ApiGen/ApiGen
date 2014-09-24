<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Generator\Resolvers;

use ApiGen\Configuration\Configuration;
use ApiGen\FileSystem;
use Nette;


/**
 * Resolves relative path to elements extracted by Generator.
 *
 * @method  RelativePathResolver setSymlinks(array)
 */
class RelativePathResolver extends Nette\Object
{

	/**
	 * @var array
	 */
	private $symlinks;

	/**
	 * @var Configuration|\stdClass
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
		foreach ($this->configuration->source as $source) {
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
