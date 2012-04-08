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
 * ApiGen environment helpers.
 */
class Environment
{
	/**
	 * Returns if ApiGen is installed as a PEAR package.
	 *
	 * @return boolean
	 */
	public static function isPearPackage()
	{
		return false === strpos('@php_dir@', '@php_dir');
	}

	/**
	 * Returns the application name.
	 *
	 * @return string
	 */
	public static function getApplicationName()
	{
		return 'ApiGen ' . VERSION;
	}
}
