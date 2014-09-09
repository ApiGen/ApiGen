<?php

namespace ApiGen\Metrics;


/**
 * Checks memory usage.
 */
interface MemoryLimitChecker
{

	/**
	 * Checks if there is enough free memory.
	 * @throws \Nette\InvalidStateException If there is no enough free memory left
	 */
	public function check();

}
