<?php

if (@!include __DIR__ . '/../../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

date_default_timezone_set('Europe/Prague');
Tester\Environment::setup();


define('TEMP_DIR', createTempDir());
Tracy\Debugger::$logDirectory = TEMP_DIR;


define('API_DIR', dirname(TEMP_DIR) . DIRECTORY_SEPARATOR . 'api');
define('APIGEN_BIN', 'php ' . realpath(__DIR__ . '/../../apigen'));


/** @return string */
function createTempDir()
{
	@mkdir(__DIR__ . '/../tmp'); // @ - directory may exists
	@mkdir($tempDir = __DIR__ . '/../tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
	Tester\Helpers::purge($tempDir);

	return realpath($tempDir);
}


function run(Tester\TestCase $testCase) {
	$testCase->run();
}
