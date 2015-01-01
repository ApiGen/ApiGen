<?php

/**
 * This file is part of the ApiGen (http://apigen.org)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */


require __DIR__ . '/bootstrap.php';


// Create temp dir
$tempDir = sys_get_temp_dir() . '/_apigen.temp';
ApiGen\FileSystem\FileSystem::purgeDir($tempDir); // clean old cache


// Init debugger
Tracy\Debugger::$strictMode = TRUE;
if (isset($_SERVER['argv']) && ($tmp = array_search('--debug', $_SERVER['argv'], TRUE))) {
	Tracy\Debugger::enable(Tracy\Debugger::DEVELOPMENT);
	define('LOG_DIRECTORY', __DIR__ . '/../apigen-log/');

} else {
	Tracy\Debugger::enable(Tracy\Debugger::PRODUCTION);
	Tracy\Debugger::$onFatalError[] = function() {
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


$configurator = new Nette\Configurator;
$configurator->setDebugMode( ! Tracy\Debugger::$productionMode);
$configurator->setTempDirectory($tempDir);
$configurator->addConfig(__DIR__ . '/ApiGen/DI/config.neon');
$container = $configurator->createContainer();


/** @var ApiGen\Console\Application $application */
$application = $container->getByType('ApiGen\Console\Application');
$application->run();
