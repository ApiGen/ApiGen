<?php

function includeIfExists(string $file): bool
{
	return file_exists($file) ? include $file : false;
}

if ( ! ($loader = includeIfExists(__DIR__ . '/../vendor/autoload.php'))
	&& ! ($loader = includeIfExists(__DIR__ . '/../../../autoload.php')))
{
	echo 'Missing autoload.php, update by the composer.' . PHP_EOL;
	exit(1);
}

return $loader;
