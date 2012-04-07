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

/**
 * ProgressBar interface.
 */
interface IProgressBar
{
	/**
	 * Initializes the progressbar.
	 *
	 * @param integer $maximum Maximum value
	 * @return \ApiGen\IProgressBar
	 */
	public function init($maximum = 1);

	/**
	 * Increments the progressbar.
	 *
	 * @param integer $increment Increment
	 * @return \ApiGen\IProgressBar
	 */
	public function increment($increment = 1);
}
