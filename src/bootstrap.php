<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */


if ( ! defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}


function includeIfExists($file)
{
	return is_file($file) ? include $file : FALSE;
}


if ( ! ($loader = includeIfExists(__DIR__ . '/../vendor/autoload.php'))
	&& ! ($loader = includeIfExists(__DIR__ . '/../../../autoload.php')))
{
	echo 'Missing autoload.php, update by the composer.' . PHP_EOL;
	exit(2);
}


include __DIR__ . DS . 'memory.php';

setMemoryLimitTo('1024M');

return $loader;
