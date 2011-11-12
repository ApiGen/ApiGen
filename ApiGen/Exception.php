<?php

/**
 * ApiGen 2.3.0 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

/**
 * ApiGen exception.
 *
 * This is the topmost exception class. Exceptions of this class are caught in the apigen.php script.
 *
 * @author Jaroslav Hanslík
 */
class Exception extends \Exception
{
	/**
	 * Invalid configuration.
	 *
	 * @var integer
	 */
	const INVALID_CONFIG = 1;
}
