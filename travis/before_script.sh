#!/bin/bash

vendor/bin/phpcs src tests --extensions=php --ignore=bootstrap,expected.php,source.php \
	--standard=vendor/zenify/coding-standard/src/ZenifyCodingStandard/ruleset.xml -p
