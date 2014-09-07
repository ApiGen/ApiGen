<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console;


interface ProgressBar
{

	/**
	 * @param integer $maximum
	 * @return ProgressBar
	 */
	public function init($maximum = 1);


	/**
	 * @param integer $increment
	 * @return ProgressBar
	 */
	public function increment($increment = 1);

}
