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

namespace ApiGen\Contrib;

use ApiGen\ConsoleLogger;
use ApiGen\IProgressBar;
use ApiGen\Object;

/**
 * PHPUnit style console progressbar.
 *
 * Requires the console logger {@see \ApiGen\ConsoleLogger}.
 */
class PhpunitProgressBar extends Object implements IProgressBar
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
	* Relative step size.
	*
	* @var float
	*/
	private $stepSize = 1;

	/**
	* Maximum line width.
	*
	* @var integer
	*/
	protected $maxWidth = 80;

	/**
	* Maximum number of steps.
	*
	* @var integer
	*/
	protected $maxSteps = 600;

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
	 * @return \ApiGen\Contrib\PhpunitProgressBar
	 */
	public function init($maximum = 1)
	{
		$this->current = 0;
		$this->maximum = (int) $maximum;

		if ($this->maxSteps < $this->maximum) {
			$this->stepSize = $this->maxSteps / max(1, $this->maximum);
		} else {
			$this->stepSize = 1;
		}

		return $this;
	}

	/**
	 * Increments the progressbar.
	 *
	 * @param integer $increment Increment
	 * @return \ApiGen\Contrib\PhpunitProgressBar
	 */
	public function increment($increment = 1)
	{
		if ($this->current >= $this->maximum) {
			// No progressbar beyond 100 %
			return;
		}

		$currentSteps = round($this->current * $this->stepSize);
		$this->current += $increment;
		$newSteps = round($this->current * $this->stepSize);

		if ($newSteps === $currentSteps) {
			// No progress
			return $this;
		}

		$maxSteps = round($this->maximum * $this->stepSize);

		// Max steps per line
		$perLine = $this->maxWidth - 10;

		while ($currentSteps++ < $newSteps) {
			$this->logger->log('.');

			// End of line or 100 % reached
			if ($this->maximum === $this->current || (0 === $currentSteps % $perLine)) {
				if ($this->maximum === $this->current) {
					$this->logger->log(str_repeat(' ', 1 + $perLine - ($currentSteps % $perLine)));
				} else {
					$this->logger->log(' ');
				}

				$this->logger->log(sprintf("[%3d %%]\n", 100 * $currentSteps / $maxSteps));
			}
		}

		return $this;
	}
}
