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
 */
class ProgressBar implements IProgressBar
{
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
	 * Initializes the progressbar.
	 *
	 * @param integer $maximum Maximum value
	 * @return \ApiGen\ProgressBar
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
	 * @return \ApiGen\ProgressBar
	 */
	public function increment($increment = 1)
	{
		static $width = 80;
		static $barWidth = 64;

		echo str_repeat(chr(0x08), $width);

		$this->current += (int) $increment;

		$percent = $this->current / $this->maximum;

		$progress = str_pad(str_pad('>', round($percent * $barWidth), '=', STR_PAD_LEFT), $barWidth, ' ', STR_PAD_RIGHT);

		echo sprintf('[%s] %\' 6.2f%% %\' 3dMB', $progress, $percent * 100, round(memory_get_usage(true) / 1024 / 1024));

		if ($this->current === $this->maximum) {
			echo "\n";
		}

		return $this;
	}
}
