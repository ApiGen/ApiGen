<?php

/**
 * ApiGen 2.1 dev - API documentation generator.
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
 * Function reflection envelope.
 *
 * Alters TokenReflection\IReflectionFunction functionality for ApiGen.
 *
 * @author Jaroslav Hanslík
 */
class ReflectionFunction extends ReflectionFunctionBase
{
	/**
	 * Returns if the function should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (null === $this->isDocumented && parent::isDocumented()) {
			foreach (self::$config->skipDocPath as $mask) {
				if (fnmatch($mask, $this->reflection->getFilename(), FNM_NOESCAPE)) {
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
