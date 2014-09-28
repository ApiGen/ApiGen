<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

use Nette;


class Factory extends Nette\Object
{

	/**
	 * @return string
	 */
	public static function getApiGenFile()
	{
		return getcwd() . '/apigen.neon';
	}


	/**
	 * Returns templates directory path.
	 *
	 * @return string
	 */
	public static function getTemplatesDir()
	{
		return realpath(APIGEN_ROOT_PATH . '/templates/');
	}


	/**
	 * The default template configuration file path.
	 *
	 * @return string
	 */
	public static function getDefaultTemplateConfig()
	{
		return self::getTemplatesDir() . '/default/config.neon';
	}

}
