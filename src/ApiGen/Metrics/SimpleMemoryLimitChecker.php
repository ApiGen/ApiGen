<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Metrics;

use Nette;


class SimpleMemoryLimitChecker extends Nette\Object implements MemoryLimitChecker
{

	/**
	 * {@inheritdoc}
	 */
	public function check()
	{
		static $limit = NULL;
		if ($limit === NULL) {
			$value = ini_get('memory_limit');
			$unit = substr($value, -1);
			if ($value === '-1') {
				$limit = 0;

			} elseif ($unit === 'G') {
				$limit = (int) $value * 1024 * 1024 * 1024;

			} elseif ($unit === 'M') {
				$limit = (int) $value * 1024 * 1024;

			} else {
				$limit = (int) $value;
			}
		}

		if ($limit && memory_get_usage(TRUE) / $limit >= 0.9) {
			throw new \RuntimeException(sprintf('Used %d %% of the current memory limit, please increase the limit to generate the whole documentation.', round(memory_get_usage(TRUE) / $limit * 100)));
		}
	}

}
