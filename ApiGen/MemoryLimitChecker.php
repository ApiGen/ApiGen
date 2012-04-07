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

use Nette\InvalidStateException;

class MemoryLimitChecker implements IMemoryLimitChecker
{
	/**
	 * Checks if there is enough free memory.
	 *
	 * @throws \Nette\InvalidStateException If there is no enough free memory left
	 */
	public function check()
	{
		static $limit = null;
		if (null === $limit) {
			$value = ini_get('memory_limit');
			$unit = substr($value, -1);
			if ('-1' === $value) {
				$limit = 0;
			} elseif ('G' === $unit) {
				$limit = (int) $value * 1024 * 1024 * 1024;
			} elseif ('M' === $unit) {
				$limit = (int) $value * 1024 * 1024;
			} else {
				$limit = (int) $value;
			}
		}

		echo 'check';

		if ($limit && memory_get_usage(true) / $limit >= 0.9) {
			throw new InvalidStateException(sprintf('Used %d%% of the current memory limit, please increase the limit to generate the whole documentation.', round(memory_get_usage(true) / $limit * 100)));
		}
	}
}
