<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Theme;

use ApiGen\Configuration\Exceptions\ConfigurationException;


class ThemeConfigPathResolver
{

	/**
	 * @var string
	 */
	private $rootDir;


	/**
	 * @param string $rootDir
	 */
	public function __construct($rootDir)
	{
		$this->rootDir = $rootDir;
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public function resolve($path)
	{
		$absolutePath = $this->rootDir . '/' . ltrim($path, DIRECTORY_SEPARATOR);
		if (file_exists($absolutePath)) {
			return $absolutePath;
		}

		throw new ConfigurationException('Config "' . $path . "' was not found in '" . $absolutePath . "'");
	}

}
