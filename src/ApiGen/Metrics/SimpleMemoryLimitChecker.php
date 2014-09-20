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
	 * @var int
	 */
	private $limit;


	/**
	 * {@inheritdoc}
	 */
	public function check()
	{
		if ($this->limit === NULL) {
			$value = ini_get('memory_limit');
			$unit = substr($value, -1);
			if ($value === '-1') {
				$this->limit = 0;

			} elseif ($unit === 'G') {
				$this->limit = (int) $value * 1024 * 1024 * 1024;

			} elseif ($unit === 'M') {
				$this->limit = (int) $value * 1024 * 1024;

			} else {
				$this->limit = (int) $value;
			}
		}

		if ($this->limit && memory_get_usage(TRUE) / $this->limit >= 0.9) {
			$relative = round(memory_get_usage(TRUE) / $this->limit * 100);
			throw new \RuntimeException(sprintf('Used %d %% of the current memory limit, please increase the limit to generate the whole documentation.', $relative));
		}
	}

}
