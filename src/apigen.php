<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

use ApiGen\FileSystem\FileSystem;
use Nette\Configurator;
use Tracy\Debugger;


require __DIR__ . '/bootstrap.php';


// Create temp dir
$tempDir = getcwd() . '/_apigen.temp';
mkdir($tempDir, 0755, TRUE);

// Delete on error
Debugger::$onFatalError[] = function() use ($tempDir) {
	FileSystem::deleteDir($tempDir);
};


// Init debugger
Debugger::$strictMode = TRUE;
if (isset($_SERVER['argv']) && ($tmp = array_search('--debug', $_SERVER['argv'], TRUE))) {
	Debugger::enable(Debugger::DEVELOPMENT);
	unset($_SERVER['argv'][$tmp]);
	$_SERVER['argv'] = array_values($_SERVER['argv']);
	$_SERVER['argc']--;
	define('LOG_DIRECTORY', __DIR__ . '/../apigen-log/');

} else {
	Debugger::enable(Debugger::PRODUCTION);
	Debugger::$onFatalError[] = function() {
		echo "For more information turn on the debug mode using the --debug option.\n";
	};
}


// Safe locale and timezone
setlocale(LC_ALL, 'C');
if ( ! ini_get('date.timezone')) {
	date_default_timezone_set('UTC');
}

// ApiGen root path
define('APIGEN_ROOT_PATH', __DIR__);


// Build the DIC
$configurator = new Configurator;
$configurator->setDebugMode( ! Debugger::$productionMode);
$configurator->setTempDirectory($tempDir);
$configurator->addConfig(__DIR__ . '/ApiGen/DI/config.neon');
$container = $configurator->createContainer();

// Let's rock
$container->getByType('ApiGen\Console\Application')->run();

// Remove temp dir
FileSystem::deleteDir($tempDir);
