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
	 * @return mixed
	 */
	public function read()
	{
		$json = file_get_contents($this->path);
		return Neon::decode($json);
	}


	/**
	 * @deprecated Only for current tests. Will be removed in 4.0.0.
	 * @param array $content
	 * @throws \Exception
	 */
	public function write(array $content)
	{
		$dir = dirname($this->path);
		if ( ! is_dir($dir)) {
			if (file_exists($dir)) {
				throw new \UnexpectedValueException($dir . ' exists and is not a directory.');
			}
			if ( ! @mkdir($dir, 0777, TRUE)) {
				throw new \UnexpectedValueException($dir . ' does not exist and could not be created.');
			}
		}
		$retries = 3;
		while ($retries--) {
			try {
				file_put_contents($this->path, Neon::encode($content, Neon::BLOCK));
				break;
			} catch (\Exception $e) {
				if ($retries) {
					usleep(500000);
					continue;
				}
				throw $e;
			}
		}
	}

}
