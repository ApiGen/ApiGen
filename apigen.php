#!/usr/bin/env php
<?php

/**
 * ApiGen - API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Apigen;

use Nette\Diagnostics\Debugger;

define('LIBRARY_DIR', realpath(__DIR__ . '/libs'));
define('TEMPLATE_DIR', realpath(__DIR__ . '/templates'));

require LIBRARY_DIR . '/Nette/nette.min.php';
require LIBRARY_DIR . '/fshl/fshl.php';
require LIBRARY_DIR . '/texy/texy.min.php';
require LIBRARY_DIR . '/TokenReflection/tokenreflection.min.php';
require LIBRARY_DIR . '/Apigen/Exception.php';
require LIBRARY_DIR . '/Apigen/Config.php';
require LIBRARY_DIR . '/Apigen/Template.php';
require LIBRARY_DIR . '/Apigen/ReflectionBase.php';
require LIBRARY_DIR . '/Apigen/ReflectionClass.php';
require LIBRARY_DIR . '/Apigen/ReflectionConstant.php';
require LIBRARY_DIR . '/Apigen/ReflectionFunctionBase.php';
require LIBRARY_DIR . '/Apigen/ReflectionFunction.php';
require LIBRARY_DIR . '/Apigen/ReflectionMethod.php';
require LIBRARY_DIR . '/Apigen/ReflectionProperty.php';
require LIBRARY_DIR . '/Apigen/ReflectionParameter.php';
require LIBRARY_DIR . '/Apigen/Backend.php';
require LIBRARY_DIR . '/Apigen/Generator.php';
require LIBRARY_DIR . '/Apigen/Tree.php';

try {

	Debugger::$strictMode = true;
	Debugger::enable();
	Debugger::timer();

	$config = new Config();

	// Help
	if ($config->isHelpRequested()) {
		echo Generator::getHeader();
		echo Config::getHelp();
		die();
	}

	$config->parse();

	// Start
	$generator = new Generator($config);
	$generator->output(Generator::getHeader());

	// Scan
	if (count($config->source) > 1) {
		$generator->output(sprintf("Scanning\n %s\n", implode("\n ", $config->source)));
	} else {
		$generator->output(sprintf("Scanning %s\n", $config->source[0]));
	}
	if (count($config->exclude) > 1) {
		$generator->output(sprintf("Excluding\n %s\n", implode("\n ", $config->exclude)));
	} elseif (!empty($config->exclude)) {
		$generator->output(sprintf("Excluding %s\n", $config->exclude[0]));
	}
	$parsed = $generator->parse();
	$generator->output(vsprintf("Found %d classes, %d constants, %d functions and other %d used PHP internal classes\n", array_slice($parsed, 0, 4)));
	$generator->output(vsprintf("Documentation for %d classes, %d constants, %d functions and other %d used PHP internal classes will be generated\n", array_slice($parsed, 4, 4)));

	// Generating
	$generator->output(sprintf("Using template config file %s\n", $config->templateConfig));

	if ($config->wipeout && is_dir($config->destination)) {
		$generator->output("Wiping out destination directory\n");
		if (!$generator->wipeOutDestination()) {
			throw new Exception('Cannot wipe out destination directory');
		}
	}

	$generator->output(sprintf("Generating to directory %s\n", $config->destination));
	$skipping = array_merge($config->skipDocPath, $config->skipDocPrefix);
	if (count($skipping) > 1) {
		$generator->output(sprintf("Will not generate documentation for\n %s\n", implode("\n ", $skipping)));
	} elseif (!empty($skipping)) {
		$generator->output(sprintf("Will not generate documentation for %s\n", $skipping[0]));
	}
	$generator->generate();

	// End
	$generator->output(sprintf("Done. Total time: %d seconds, used: %d MB RAM\n", Debugger::timer(), round(memory_get_peak_usage(true) / 1024 / 1024)));

} catch (Exception $e) {
	if ($e instanceof Exception && Exception::INVALID_CONFIG === $e->getCode()) {
		echo Generator::getHeader();
	}

	if ($config->debug) {
		do {
			printf("\n%s", $e->getMessage());
			$trace = $e->getTraceAsString();
		} while ($e = $e->getPrevious());

		printf("\n\n%s\n\n", $trace);
	} else {
		printf("\n%s\n\n", $e->getMessage());
	}

	// Help only for invalid configuration
	if ($e instanceof Exception && Exception::INVALID_CONFIG === $e->getCode()) {
		echo Config::getHelp();
	}

	die(1);
}