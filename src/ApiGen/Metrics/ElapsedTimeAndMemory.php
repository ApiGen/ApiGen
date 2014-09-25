<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Metrics;

use ApiGen\Logger;
use Nette;


/**
 * Computes and prints the elapsed time and memory.
 */
class ElapsedTimeAndMemory extends Nette\Object
{

	/**
	 * @var Logger
	 */
	private $logger;


	public function __construct(Logger $logger)
	{
		$this->logger = $logger;
	}


	public function printElapsed(\DateTime $start, \DateTime $end)
	{
		$interval = $end->diff($start);

		$parts = array();
		if ($interval->h > 0) {
			$parts[] = sprintf('%d hours', $interval->h);
		}

		if ($interval->i > 0) {
			$parts[] = sprintf('%d min', $interval->i);
		}

		if ($interval->s > 0) {
			$parts[] = sprintf('%d sec', $interval->s);
		}

		if (empty($parts)) {
			array_push($parts, ' %d sec', 1);
		}

		$duration = implode(' ', $parts);
		$usedMemory = round(memory_get_peak_usage(TRUE) / 1024 / 1024);

		$this->logger->log(sprintf("Done. Total time: %s, used: %d MB RAM\n", $duration, $usedMemory));
	}

}
