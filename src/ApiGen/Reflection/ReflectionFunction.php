<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\FileSystem\FileSystem;
use TokenReflection;


class ReflectionFunction extends ReflectionFunctionBase
{

	/**
	 * @return bool
	 */
	public function isValid()
	{
		if ($this->reflection instanceof TokenReflection\Invalid\ReflectionFunction) {
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * @return bool
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL && parent::isDocumented()) {
			$fileName = FileSystem::unPharPath($this->reflection->getFilename());
			foreach (self::$config->skipDocPath as $mask) {
				if (fnmatch($mask, $fileName, FNM_NOESCAPE)) {
					$this->isDocumented = FALSE;
					break;
				}
			}
		}
		return $this->isDocumented;
	}

}
