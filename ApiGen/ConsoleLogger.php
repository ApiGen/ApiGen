<?php

namespace ApiGen;

use ApiGen\Config\Configuration;
use ApiGen\Environment;

/**
 * Console logger.
 *
 * Uses STDOUT as output stream.
 */
class ConsoleLogger implements ILogger
{
	/**
	 * Application configuration
	 *
	 * @var \ApiGen\Config\Configuration
	 */
	private $config;

	/**
	 * Has output already started?
	 *
	 * @var boolean
	 */
	private $outputStarted = false;

	/**
	 * Colorization placeholders.
	 *
	 * @var array
	 */
	private $placeholders = array(
		'@header@' => "\x1b[1;34m",
		'@count@' => "\x1b[1;34m",
		'@option@' => "\x1b[0;36m",
		'@value@' => "\x1b[0;32m",
		'@error@' => "\x1b[0;31m",
		'@c' => "\x1b[0m"
	);

	/**
	 * Creates the logger and initializes colorization.
	 *
	 * @param \ApiGen\Config\Configuration $config Application configuration
	 */
	public function __construct(Configuration $config)
	{
		$this->config = $config;

		if (null === $this->config || !$this->config->colors) {
			$this->placeholders = array_fill_keys(array_keys($this->placeholders), '');
		}
	}

	/**
	 * Logs a message.
	 *
	 * @param string $message Message
	 */
	public function log($message)
	{
		if (!$this->config->quiet) {
			if (!$this->outputStarted) {
				// Print out the header right before the first message
				$this->prepareOutput($this->getHeader());
				$this->outputStarted = true;
			}

			fputs(STDOUT, $this->prepareOutput($message));
		}
	}

	/**
	 * Log en exception.
	 *
	 * @param \Exception $e Exception
	 */
	public function logException(Exception $e)
	{
		if ($e instanceof ConfigException) {
			$this->log(sprintf("\n@error@%s@c\n\n", $e->getMessage()));
		} else {
			if ($this->config->debug) {
				do {
					$this->log(sprintf("\n%s(%d): @error@%s@c", $e->getFile(), $e->getLine(), $e->getMessage()));
					$trace = $e->getTraceAsString();
				} while ($e = $e->getPrevious());

				$this->log(printf("\n\n%s\n", $trace));
			} else {
				$this->log(sprintf("\n@error@%s@c\n", $e->getMessage()));
			}
		}
	}

	/**
	 * Returns the output header.
	 *
	 * @return string
	 */
	private function getHeader()
	{
		$name = sprintf("%s %s", $this->config->application->name, $this->config->application->version);
		return sprintf("@header@%s@c\n%s\n", $name, str_repeat('-', strlen($name)));
	}

	/**
	 * Prepares (colorizes) the message.
	 *
	 * @param string $message Message
	 * @return string
	 */
	protected function prepareOutput($message)
	{
		return strtr($message, $this->placeholders);
	}
}
