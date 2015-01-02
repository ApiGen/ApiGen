<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace ApiGen\Console\Input;

use Symfony;


class LiberalFormatArgvInput extends Symfony\Component\Console\Input\ArgvInput
{

	/**
	 * @return array
	 */
	public function getOptions()
	{
		$options = parent::getOptions();
		foreach ($options as $key => $value) {
			$options[$key] = $this->removeEqualsSign($value);
			$options[$key] = $this->splitByComma($value);
		}
		return $options;
	}


	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getOption($name)
	{
		$this->options = $this->getOptions();
		return parent::getOption($name);
	}


	/**
	 * @param array|string $value
	 * @return array|string
	 */
	private function removeEqualsSign($value)
	{
		if (is_array($value)) {
			array_walk($value, function (&$singleValue) {
				$singleValue = ltrim($singleValue, '=');
			});

		} else {
			$value = ltrim($value, '=');
		}
		return $value;
	}


	/**
	 * @param mixed $value
	 * @return mixed
	 */
	private function splitByComma($value)
	{
		if (is_array($value) && count($value) === 1) {
			array_walk($value, function (&$singleValue) {
				if ($this->containsComma($singleValue)) {
					$singleValue = explode(',', $singleValue);
				}
			});
			if (is_array($value[0])) {
				return $value[0];
			}
		}

		if ($this->containsComma($value)) {
			$value = explode(',', $value);
		}
		return $value;
	}


	/**
	 * @param string $value
	 * @return bool
	 */
	private function containsComma($value)
	{
		return strpos($value, ',') !== FALSE;
	}

}
