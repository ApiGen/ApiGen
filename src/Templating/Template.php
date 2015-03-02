<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
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

		file_put_contents($this->savePath, $this->render());
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


	/**
	 * @return string
	 */
	public function render()
	{
		ob_start();
		$this->latte->render($this->file, $this->parameters);
		ob_end_clean();
	}

}
