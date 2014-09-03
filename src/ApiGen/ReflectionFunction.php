<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen;

/**
 * Function reflection envelope.
 *
 * Alters TokenReflection\IReflectionFunction functionality for ApiGen.
 */
class ReflectionFunction extends ReflectionFunctionBase
{
	/**
	 * Returns if the function is valid.
	 *
	 * @return boolean
	 */
	public function isValid()
	{
		if ($this->reflection instanceof \TokenReflection\Invalid\ReflectionFunction) {
			return false;
		}

		return true;
	}

	/**
	 * Returns if the function should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (null === $this->isDocumented && parent::isDocumented()) {
			$fileName = self::$generator->unPharPath($this->reflection->getFilename());
			foreach (self::$config->skipDocPath as $mask) {
				if (fnmatch($mask, $fileName, FNM_NOESCAPE)) {
					$this->isDocumented = false;
					break;
				}
			}
			if (true === $this->isDocumented) {
				foreach (self::$config->skipDocPrefix as $prefix) {
					if (0 === strpos($this->reflection->getName(), $prefix)) {
						$this->isDocumented = false;
						break;
					}
				}
			}
		}

		return $this->isDocumented;
	}
}
