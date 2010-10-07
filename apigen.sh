#!/bin/bash

if [ -f /etc/alternatives/php ] ; then
	/etc/alternatives/php "apigen.php" $*
elif [ -f /usr/bin/php ] ; then
	/usr/bin/php "apigen.php" $*
else
	echo "PHP binary not found"
fi
