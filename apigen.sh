#!/bin/bash

PHP_BINARY=$(which php)
if [ $? -ne 0 ]; then
	echo "PHP binary not found"
	exit 1
fi

$PHP_BINARY "apigen.php" $*
