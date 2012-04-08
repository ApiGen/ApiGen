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

use ApiGen\Config;
use Nette\Diagnostics\Debugger;

// Load ApiGen basic environment and check requirements and dependencies
require __DIR__ . '/ApiGen/loader.php';

// Init Nette debugger
Debugger::$strictMode = true;
Debugger::$onFatalError[] = function() {
	echo "\nFor more information turn on the debug mode using the --debug option.\n";
};
Debugger::enable(Debugger::PRODUCTION, false);

// Parse console input
$parser = new ConsoleParser();
$arguments = $parser->parseArguments(array_slice($argv, 1));

// Build the DIC
$configurator = new Config\Configurator($arguments);
$context = $configurator->createContainer();

// Update debugger configuration if needed
if ($context->apigen->config->debug) {
	Debugger::enable(Debugger::DEVELOPMENT, false);
	Debugger::$onFatalError = array();
}

// Let's rock
$context->apigen->application->run();
