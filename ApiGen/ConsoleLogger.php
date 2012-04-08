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

namespace ApiGen;

use ApiGen\Config\Configuration;
use ApiGen\Environment;
use Exception;
use Nette\InvalidStateException;

/**
 * Console logger.
 *
 * Uses STDOUT as output stream.
 */
class ConsoleLogger extends Object implements ILogger
{
	/**
	 * Do not output anything at all.
	 *
	 * @var boolean
	 */
	private $quiet;

	/**
	 * ApiGen runs in debug mode.
	 *
	 * @var boolean
	 */
	private $debug;

	/**
	 * Colorization placeholders.
	 *
	 * @var array
	 */
	private $placeholders = array(
		self::TYPE_HEADER => "\x1b[1;34m",
		self::TYPE_NUMBER => "\x1b[1;34m",
		self::TYPE_OPTION => "\x1b[0;36m",
		self::TYPE_VALUE => "\x1b[0;32m",
		self::TYPE_ERROR => "\x1b[0;31m"
	);

	/**
	 * Colorization clearer.
	 *
	 * @var string
	 */
	private $clearer = "\x1b[0m";

	/**
	 * Creates the logger and initializes colorization.
	 *
	 * @param boolean $quiet Do not output anything at all
	 * @param boolean $colors Use colors for output
	 * @param boolean $debug ApiGen runs in debug mode
	 */
	public function __construct($quiet, $colors, $debug)
	{
		$this->quiet = $quiet;
		$this->debug = $debug;

		if (!$colors) {
			$this->placeholders = array_fill_keys(array_keys($this->placeholders), '');
			$this->clearer = '';
		}
	}

	/**
	 * Log a message.
	 *
	 * Takes one argument as the messasge format definition and variable number of other arguments
	 * that act like placeholder values (imagine sprintf).
	 *
	 * @param string $message Message format
	 * @param string $arg,...
	 */
	public function log($message)
	{
		if (!$this->quiet) {
			static $pattern;
			if (!isset($pattern)) {
				$pattern = '~(' . implode('|', array_map('preg_quote', array_keys($this->placeholders))) . ')~';
			}

			$placeholders = $this->placeholders;
			$clearer = $this->clearer;

			$output = '';

			$args = func_get_args();
			while (!empty($args)) {
				$output .= preg_replace_callback($pattern, function($matches) use (&$args, $placeholders, $clearer) {
					if (empty($args)) {
						throw new InvalidStateException('Not enough values for all placeholders.');
					}

					return $placeholders[$matches[1]] . array_shift($args) . $clearer;
				}, array_shift($args));
			}

			fputs(STDOUT, $output);
		}
	}

	/**
	 * Log en exception.
	 *
	 * @param \Exception $e Exception
	 */
	public function logException(Exception $e)
	{
		if ($e instanceof Config\Exception) {
			$this->log("\n", self::TYPE_ERROR, $e->getMessage(), "\n\n");
		} else {
			if ($this->debug) {
				do {
					$this->log(sprintf("\n%s(%d): ", $e->getFile(), $e->getLine()), self::TYPE_ERROR, $e->getMessage());
					$trace = $e->getTraceAsString();
				} while ($e = $e->getPrevious());

				$this->log(printf("\n\n%s\n", $trace));
			} else {
				$this->log("\n", self::TYPE_ERROR, $e->getMessage(), "\n");
			}
		}
	}
}
