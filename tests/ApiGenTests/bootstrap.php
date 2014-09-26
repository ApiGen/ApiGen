<?php

if (@!include __DIR__ . '/../../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

date_default_timezone_set('Europe/Prague');
Tester\Environment::setup();


if ( ! defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}


define('TEMP_DIR', createTempDir());
Tracy\Debugger::$logDirectory = TEMP_DIR;


define('PROJECT_DIR', __DIR__ . DS . 'ApiGen/Project');
define('API_DIR', TEMP_DIR . DS . 'api');
define('APIGEN_BIN', realpath(__DIR__ . '/../../bin/apigen'));


/** @return string */
function createTempDir() {
	@mkdir(__DIR__ . '/../tmp'); // @ - directory may exists
	@mkdir($tempDir = __DIR__ . '/../tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
	Tester\Helpers::purge($tempDir);

	return realpath($tempDir);
}


function run(Tester\TestCase $testCase) {
	$testCase->run();
}
