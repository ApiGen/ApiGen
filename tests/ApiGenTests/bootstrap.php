<?php

if (@!include __DIR__ . '/../../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

date_default_timezone_set('Europe/Prague');
Tester\Environment::setup();


define('TEMP_DIR', createTempDir());
Tracy\Debugger::$logDirectory = TEMP_DIR;


define('API_DIR', TEMP_DIR . DIRECTORY_SEPARATOR . 'api');
define('APIGEN_BIN', realpath(__DIR__ . '/../../bin/apigen'));


/** @return string */
function createTempDir() {
	@mkdir(__DIR__ . '/../tmp'); // @ - directory may exists
	@mkdir($tempDir = __DIR__ . '/../tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
	Tester\Helpers::purge($tempDir);

	return realpath($tempDir);
}


/**
 * Moves config file to temp directory and replaces paths in it.
 *
 * @param  string
 * @return string
 */
function atomicConfig($original) {
	if (!is_file($original)) {
		Tester\Assert::fail("Configuration file '$original' does not exist.");
	}

	$config = Nette\Neon\Neon::decode(file_get_contents($original));
	if (isset($config['source'])) {
		$config['source'] = array(__DIR__ . '/ApiGen/Project');
	}
	if (isset($config['destination'])) {
		$config['destination'] = API_DIR;
	}

	file_put_contents($new = TEMP_DIR . DIRECTORY_SEPARATOR . basename($original), Nette\Neon\Neon::encode($config, Nette\Neon\Encoder::BLOCK));
	return $new;
}


function run(Tester\TestCase $testCase) {
	$testCase->run();
}
