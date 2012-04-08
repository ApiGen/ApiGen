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
 * Interface for services checking for the newest version.
 */
interface IUpdateChecker
{
	/**
	 * Returns the latest version.
	 *
	 * @return string
	 */
	public function getLatestVersion();

	/**
	 * Checks if there is newer version. If so, triggers the "updateAvailable" event.
	 *
	 * @return boolean
	 */
	public function checkUpdate();
}
