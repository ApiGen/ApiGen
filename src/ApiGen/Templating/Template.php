<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Templating;

use Nette;


/**
 * @method Template setFile($file)
 * @method string   namespaceUrl(string $s)
 * @method string   packageUrl(string $s)
 * @method string   classUrl(string $s)
 * @method string   constantUrl(string $s)
 * @method string   functionUrl(string $s)
 * @method string   sourceUrl(string $s)
 */
class Template extends Nette\Bridges\ApplicationLatte\Template
{

	/**
	 * @var string
	 */
	private $savePath;


	/**
	 * @param string $file
	 */
	public function save($file = NULL)
	{
		$this->savePath = $file ?: $this->savePath;
		$dir = dirname($this->savePath);
		if ( ! is_dir($dir)) {
			mkdir($dir, 0755, TRUE);
		}

		file_put_contents($this->savePath, $this->__toString());
	}


	/**
	 * @param string $savePath
	 */
	public function setSavePath($savePath)
	{
		$this->savePath = $savePath;
	}

}
