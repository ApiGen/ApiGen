<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;


/**
 * Helper set interface.
 */
interface IHelperSet
{

	/**
	 * Tries to load the requested helper.
	 *
	 * @param string $helperName Helper name
	 * @return \Nette\Callback
	 */
	public function loader($helperName);

}
