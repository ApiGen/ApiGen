<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection\Parts;


trait StartLineEndLine
{

	/**
	 * @var int
	 */
	private $startLine;

	/**
	 * @var int
	 */
	private $endLine;


	/**
	 * @param int $startLine
	 */
	public function setStartLine($startLine)
	{
		$this->startLine = $startLine;
	}


	/**
	 * @return int
	 */
	public function getStartLine()
	{
		return $this->startLine;
	}


	/**
	 * @param int $endLine
	 */
	public function setEndLine($endLine)
	{
		$this->endLine = $endLine;
	}


	/**
	 * @return int
	 */
	public function getEndLine()
	{
		return $this->endLine;
	}

}
