<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */


/**
 * @param string $newMemoryLimit
 */
function setMemoryLimitTo($newMemoryLimit)
{
	if (function_exists('ini_set')) {
		$memoryLimit = trim(ini_get('memory_limit'));
		if ($memoryLimit !== -1 && getMemoryInBytes($memoryLimit) < 512 * 1024 * 1024) {
			@ini_set('memory_limit', $newMemoryLimit);
		}
		unset($memoryInBytes, $memoryLimit);
	}
}


/**
 * @param string $value
 * @return int
 */
function getMemoryInBytes($value)
{
	$unit = strtolower(substr($value, -1, 1));
	$value = (int) $value;
	if ($unit === 'g') {
		return $value * 1024 * 1024 * 1024;
	}
	if ($unit === 'm') {
		return $value * 1024 * 1024;
	}
	if ($unit === 'k') {
		return $value * 1024;
	}
}
