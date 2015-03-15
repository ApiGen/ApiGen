<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Configuration\Readers;


class ReaderFactory
{

	public static function getReader($path)
	{
		$configFileExt = pathinfo($path, PATHINFO_EXTENSION);

		return ($configFileExt !== 'yaml' && $configFileExt !== 'yml') ? new NeonFile($path) : new YamlFile($path);
	}

}
