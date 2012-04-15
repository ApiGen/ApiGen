<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen\Checkstyle;

use Nette;

class Message extends Nette\Object
{
	const SEVERITY_ERROR = 'error';
	const SEVERITY_WARNING = 'warning';

	private $text;

	private $line;

	private $severity;

	public function __construct($text, $line, $severity = self::SEVERITY_ERROR)
	{
		$this->text = (string) $text;
		$this->line = (int) $line;
		$this->severity = (string) $severity;
	}

	public function getText()
	{
		return $this->text;
	}

	public function getLine()
	{
		return $this->line;
	}

	public function getSeverity()
	{
		return $this->severity;
	}
}
