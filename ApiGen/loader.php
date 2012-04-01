<?php

namespace ApiGen;

use Exception;
use Nette\Diagnostics\Debugger;
use Nette\Utils\LimitedScope;

// ApiGen version
const VERSION = '3.0dev';

// ApiGen root path
define('ApiGen\\ROOT_PATH', dirname(__DIR__));

require __DIR__ . '/Environment.php';

try {
	// Check dependencies
	foreach (array('json', 'iconv', 'mbstring', 'tokenizer') as $extension) {
		if (!extension_loaded($extension)) {
			throw new Exception(sprintf("Required extension missing: %s\n", $extension), 1);
		}
	}

	if (Environment::isPearPackage()) {
		// PEAR package
		@include('Nette/loader.php');
		@include('Texy/texy.php');
	} else {
		// Standalone package
		@include ROOT_PATH . '/libs/Nette/Nette/loader.php';
		@include $file = ROOT_PATH . '/libs/Texy/texy/texy.php';

		set_include_path(
			ROOT_PATH . '/libs/FSHL' . PATH_SEPARATOR .
			ROOT_PATH . '/libs/TokenReflection' . PATH_SEPARATOR .
			get_include_path()
		);
	}

	// ApiGen autoloader
	spl_autoload_register(function($className) {
		if ('ApiGen\\' === substr($className, 0, 7) && is_file($fileName = ROOT_PATH . '/' . str_replace('\\', '/', $className . '.php'))) {
			LimitedScope::load($fileName);
		} else {
			@LimitedScope::load(str_replace('\\', '/', $className . '.php'));
		}
	});

	// Check dependencies
	if (!class_exists('Nette\\Diagnostics\\Debugger')) {
		throw new Exception('Could not find Nette framework', 2);
	}
	if (!class_exists('Texy')) {
		throw new Exception('Could not find Texy! library', 2);
	}
	if (!class_exists('FSHL\\Highlighter')) {
		throw new Exception('Could not find FSHL library', 2);
	}
	if (!class_exists('TokenReflection\\Broker')) {
		throw new Exception('Could not find TokenReflection library', 2);
	}

} catch (Exception $e) {
	fputs(STDERR, $e->getMessage() . "\n");
	exit(min(1, $e->getCode()));
}
