<?php

/**
 * ApiGen 2.6.1 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

/**
 * Simple console progressbar.
 *
 * Requires the console logger {@see \ApiGen\ConsoleLogger}.
 */
class ConsoleProgressBar implements IProgressBar
{
	/**
	 * Console logger.
	 *
	 * @var \ApiGen\ConsoleLogger
	 */
	private $logger;

	/**
	 * Current value.
	 *
	 * @var increment
	 */
	private $current = 0;

	/**
	 * Maximum value.
	 *
	 * @var increment
	 */
	private $maximum = 1;

	/**
	 * Creates the progressbar service.
	 *
	 * @param \ApiGen\ConsoleLogger $logger Console logger service.
	 */
	public function __construct(ConsoleLogger $logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Initializes the progressbar.
	 *
	 * @param integer $maximum Maximum value
	 * @return \ApiGen\ConsoleProgressBar
	 */
	public function init($maximum = 1)
	{
		$this->current = 0;
		$this->maximum = (int) $maximum;

		return $this;
	}

	/**
	 * Increments the progressbar.
	 *
	 * @param integer $increment Increment
	 * @return \ApiGen\ConsoleProgressBar
	 */
	public function increment($increment = 1)
	{
		static $width = 80;
		static $barWidth = 64;

		echo str_repeat(chr(0x08), $width);

		$this->current += (int) $increment;

		$percent = $this->current / $this->maximum;

		$progress = str_pad(str_pad('>', round($percent * $barWidth), '=', STR_PAD_LEFT), $barWidth, ' ', STR_PAD_RIGHT);

		$this->logger->log(sprintf('[%s] %\' 6.2f%% %\' 3dMB', $progress, $percent * 100, round(memory_get_usage(true) / 1024 / 1024)));

		if ($this->current === $this->maximum) {
			$this->logger->log("\n");
		}

		return $this;
	}
}
