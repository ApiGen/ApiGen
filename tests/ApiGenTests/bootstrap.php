<?php

if (@ ! include_once __DIR__ . '/../../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

include_once 'TestCase.php';

date_default_timezone_set('Europe/Prague');
Tester\Environment::setup();


if ( ! defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}


define('TEMP_DIR', createTempDir());
Tracy\Debugger::$logDirectory = TEMP_DIR;


define('PROJECT_DIR', __DIR__ . DS . '../Project');
define('PROJECT_BETA_DIR', __DIR__ . DS . '../ProjectBeta');
define('API_DIR', TEMP_DIR . DS . 'api');
define('APIGEN_BIN', 'php ' . realpath(__DIR__ . '/../../src/apigen.php'));


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
