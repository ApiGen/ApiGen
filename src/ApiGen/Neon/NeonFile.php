<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Neon;

use Nette\Neon\Neon;


class NeonFile
{

	/**
	 * @var string
	 */
	private $path;


	/**
	 * @param string $path
	 */
	public function __construct($path)
	{
		$this->validatePath($path);
		$this->path = $path;
	}


	/**
	 * @throws \Exception
	 */
	private function validatePath($path)
	{
		if ( ! file_exists($path)) {
			throw new \Exception($path . ' could not be found');
		}

		if ( ! is_readable($path)) {
			throw new \Exception ($path . ' is not readable.');
		}
	}


	/**
	 * @return array
	 */
	public function read()
	{
		$json = file_get_contents($this->path);
		return (array) Neon::decode($json);
	}

}
