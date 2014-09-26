<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Neon;

use Nette;
use Nette\Neon\Neon;


/**
 * Reads/writes neon files.
 */
class NeonFile extends Nette\Object
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
		$this->path = $path;
	}


	/**
	 * @return bool
	 */
	public function exists()
	{
		return is_file($this->path);
	}


	/**
	 * @throws \Exception
	 */
	public function validate()
	{
		if ( ! file_exists($this->path) &&  ! file_put_contents($this->path, "{\n}\n")) {
			throw new \Exception($this->path . ' could not be created');
		}

		if ( ! is_readable($this->path)) {
			throw new \Exception ($this->path . ' is not readable.');
		}

		if ( ! is_writable($this->path)) {
			throw new \Exception ($this->path . ' is not writable.');
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
				file_put_contents($this->path, Neon::encode($content, TRUE));
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
