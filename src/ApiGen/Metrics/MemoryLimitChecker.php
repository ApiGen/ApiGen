<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Metrics;


/**
 * Checks memory usage.
 */
interface MemoryLimitChecker
{

	/**
	 * Checks if there is enough free memory.
	 *
	 * @throws \Nette\InvalidStateException If there is no enough free memory left
	 */
	public function check();

}
