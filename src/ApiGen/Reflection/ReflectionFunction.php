<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Reflection;

use ApiGen\FileSystem\FileSystem;
use TokenReflection\IReflectionFunction;


/**
 * Function reflection envelope
 */
class ReflectionFunction extends ReflectionFunctionBase implements IReflectionFunction
{

	/**
	 * @return bool
	 */
	public function isValid()
	{
		if ($this->reflection instanceof \TokenReflection\Invalid\ReflectionFunction) {
			return FALSE;
		}

		return TRUE;
	}


	/**
	 * Returns if the function should be documented.
	 *
	 * @return bool
	 */
	public function isDocumented()
	{
		if ($this->isDocumented === NULL && parent::isDocumented()) {
			$fileName = FileSystem::unPharPath($this->reflection->getFilename());
			$options = $this->configuration->getOptions();
			foreach ($options['skipDocPath'] as $mask) {
				if (fnmatch($mask, $fileName, FNM_NOESCAPE)) {
					$this->isDocumented = FALSE;
					break;
				}
			}
			if ($this->isDocumented === TRUE) {
				foreach ($options['skipDocPrefix'] as $prefix) {
					if (strpos($this->reflection->getName(), $prefix) === 0) {
						$this->isDocumented = FALSE;
						break;
					}
				}
			}
		}

		return $this->isDocumented;
	}


	public function isDisabled()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function invokeArgs(array $args = array())
	{
		throw new \Exception('Not implemented nor required');
	}


	public function getClosure()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function isClosure()
	{
		throw new \Exception('Not implemented nor required');
	}


	public function getStaticVariables()
	{
		throw new \Exception('Not implemented nor required');
	}

}
