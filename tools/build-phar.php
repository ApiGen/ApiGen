<?php declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

$include = [
	'/bin/**',
	'/src/**',
	'/stubs/**',
	'/vendor/**',
	'/apigen.neon',
	'/composer.json',
	'/LICENSE',
	'/README.md',
];

$stub = <<<'STUB'
	#!/usr/bin/env php
	<?php declare(strict_types = 1);

	if (!class_exists(Phar::class)) {
		echo "Missing phar extension which is required to run ApiGen.\n";
		exit(1);
	}

	Phar::mapPhar('apigen.phar');
	require 'phar://apigen.phar/bin/apigen';
	__HALT_COMPILER();
	STUB;

$rootDir = __DIR__ . '/..';
$files = Nette\Utils\Finder::findFiles(...$include)->from($rootDir);

@unlink(__DIR__ . '/apigen.phar');
$phar = new Phar(__DIR__ . '/apigen.phar');
$phar->buildFromIterator($files, $rootDir);
$phar->setStub($stub);
chmod(__DIR__ . '/apigen.phar', 0755);
