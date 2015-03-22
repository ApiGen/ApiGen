<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen\Templating;

use Latte\Engine;


class Template
{

	/**
	 * @var Engine
	 */
	private $latte;

	/**
	 * @var string
	 */
	private $file;

	/**
	 * @var string
	 */
	private $savePath;

	/**
	 * @var mixed[]
	 */
	private $parameters = [];


	public function __construct(Engine $latte)
	{
		$this->latte = $latte;
	}


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

		$content = $this->latte->renderToString($this->file, $this->parameters);
		file_put_contents($this->savePath, $content);
	}


	/**
	 * @param string $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}


	/**
	 * @param string $savePath
	 */
	public function setSavePath($savePath)
	{
		$this->savePath = $savePath;
	}


	/**
	 * @return mixed[]
	 */
	public function getParameters()
	{
		return $this->parameters;
	}


	/**
	 * @return self
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters + $this->parameters;
		return $this;
	}

}
