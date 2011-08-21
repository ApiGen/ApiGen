<?php

/**
 * ApiGen 2.0.3 - API documentation generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
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
