#!/bin/bash

if [ $TRAVIS_PHP_VERSION = '5.6' ]; then
  PHPUNIT_FLAGS="--coverage-clover=coverage.clover"
elseif [ $TRAVIS_PHP_VERSION != '7.0' ]
  phpenv config-rm xdebug.ini
fi
