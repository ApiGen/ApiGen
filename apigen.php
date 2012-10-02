#!/usr/bin/env php
<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

use Nette\Diagnostics\Debugger;

// Check environment
try {
	require __DIR__ . '/ApiGen/Environment.php';
	Environment::init();
} catch (\Exception $e) {
	fputs(STDERR, $e->getMessage() . "\n");
	exit(min(1, $e->getCode()));
}

// Init Nette debugger
Debugger::$strictMode = true;
Debugger::$onFatalError = array();
Debugger::enable(Debugger::DEVELOPMENT, false);

// Parse console input
$parser = new ConsoleParser();
$arguments = $parser->parseArguments(array_slice($argv, 1));

// Build the DIC
$configurator = new Config\Configurator($arguments);
$context = $configurator->createContainer();

// Update debugger configuration if needed
if (!$context->apigen->config->debug) {
	Debugger::$onFatalError[] = function() {
		echo "\nFor more information turn on the debug mode using the --debug option.\n";
	};
	Debugger::enable(Debugger::PRODUCTION, false);
}

// Let's rock
$context->apigen->application->run();