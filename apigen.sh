#!/bin/bash

PHP_BINARY="/usr/bin/php"
if [ ! -f $PHP_BINARY ]; then
	PHP_BINARY=$(which php)
	if [ $? -ne 0 ]; then
		echo "PHP binary not found"
		exit 1
	fi
fi

$PHP_BINARY "apigen.php" $*
