<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;


class SimpleProgressBar implements ProgressBar
{
	/**
	 * @var int
	 */
	private $current = 0;

	/**
	 * @var int
	 */
	private $maximum = 1;


	/**
	 * @param int $maximum
	 */
	public function init($maximum = 1)
	{
		$this->current = 0;
		$this->maximum = (int) $maximum;
	}


	/**
	 * @param int $increment
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
	}

}
