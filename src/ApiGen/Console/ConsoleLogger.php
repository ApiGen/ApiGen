<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;

use ApiGen\Configuration\Configuration;
use ApiGen\Configuration\ConfigurationException;
use ApiGen\Logger;
use Exception;


/**
 * Uses STDOUT as output stream.
 */
class ConsoleLogger implements Logger
{

	/**
	 * @var Configuration
	 */
	private $config;

	/**
	 * @var boolean
	 */
	private $outputStarted = FALSE;


	public function __construct(Configuration $config)
	{
		$this->config = $config;
	}


	/**
	 * @param string $message
	 */
	public function log($message)
	{
		if ( ! $this->outputStarted) {
			// Print out the header right before the first message
			$this->getHeader();
			$this->outputStarted = TRUE;
		}

		fputs(STDOUT, $message);
	}


	public function logException(Exception $e)
	{
		if ($e instanceof ConfigurationException) {
			$this->log(sprintf("\n%s\n\n", $e->getMessage()));

		} else {
			if ($this->config->debug) {
				do {
					$this->log(sprintf("\n%s(%d): %s", $e->getFile(), $e->getLine(), $e->getMessage()));
					$trace = $e->getTraceAsString();

				} while ($e = $e->getPrevious());

				$this->log(printf("\n\n%s\n", $trace));

			} else {
				$this->log(sprintf("\n%s\n", $e->getMessage()));
			}
		}
	}


	/**
	 * @return string
	 */
	private function getHeader()
	{
		$name = sprintf("%s %s", $this->config->application->name, $this->config->application->version);
		return sprintf("%s\n%s\n", $name, str_repeat('-', strlen($name)));
	}

}
