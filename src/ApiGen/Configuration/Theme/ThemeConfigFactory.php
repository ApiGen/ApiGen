<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration\Theme;


interface ThemeConfigFactory
{

	/**
	 * @param string $path
	 * @return ThemeConfig
	 */
	function create($path);

}
