#!/usr/bin/env php
<?php

/**
 * ApiGen 2.0 - API documentation generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

use Nette\Diagnostics\Debugger;

define('ROOT_DIR', __DIR__);
define('LIBRARY_DIR', ROOT_DIR . '/libs');
define('TEMPLATE_DIR', ROOT_DIR . '/templates');

require LIBRARY_DIR . '/Nette/nette.min.php';
require LIBRARY_DIR . '/FSHL/fshl.min.php';
require LIBRARY_DIR . '/Texy/texy.min.php';
require LIBRARY_DIR . '/TokenReflection/tokenreflection.min.php';

spl_autoload_register(function($class) {
	require_once sprintf('%s%s%s.php', ROOT_DIR, DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, $class));
});

try {

	Debugger::$strictMode = true;
	Debugger::enable();
	Debugger::timer();

	$config = new Config();
	$generator = new Generator($config);

	// Help
	if ($config->isHelpRequested()) {
		echo $generator->colorize($generator->getHeader());
		echo $generator->colorize($config->getHelp());
		die();
	}

	// Start
	$config->parse();
	$generator->output($generator->getHeader());

	// Scan
	if (count($config->source) > 1) {
		$generator->output(sprintf("Scanning\n @value@%s@c\n", implode("\n ", $config->source)));
	} else {
		$generator->output(sprintf("Scanning @value@%s@c\n", $config->source[0]));
	}
	if (count($config->exclude) > 1) {
		$generator->output(sprintf("Excluding\n @value@%s@c\n", implode("\n ", $config->exclude)));
	} elseif (!empty($config->exclude)) {
		$generator->output(sprintf("Excluding @value@%s@c\n", $config->exclude[0]));
	}
	$parsed = $generator->parse();
	$generator->output(vsprintf("Found @count@%d@c classes, @count@%d@c constants, @count@%d@c functions and other @count@%d@c used PHP internal classes\n", array_slice($parsed, 0, 4)));
	$generator->output(vsprintf("Documentation for @count@%d@c classes, @count@%d@c constants, @count@%d@c functions and other @count@%d@c used PHP internal classes will be generated\n", array_slice($parsed, 4, 4)));

	// Generating
	$generator->output(sprintf("Using template config file @value@%s@c\n", $config->templateConfig));

	if ($config->wipeout && is_dir($config->destination)) {
		$generator->output("Wiping out destination directory\n");
		if (!$generator->wipeOutDestination()) {
			throw new Exception('Cannot wipe out destination directory');
		}
	}

	$generator->output(sprintf("Generating to directory @value@%s@c\n", $config->destination));
	$skipping = array_merge($config->skipDocPath, $config->skipDocPrefix);
	if (count($skipping) > 1) {
		$generator->output(sprintf("Will not generate documentation for\n @value@%s@c\n", implode("\n ", $skipping)));
	} elseif (!empty($skipping)) {
		$generator->output(sprintf("Will not generate documentation for @value@%s@c\n", $skipping[0]));
	}
	$generator->generate();

	// End
	$generator->output(sprintf("Done. Total time: @count@%d@c seconds, used: @count@%d@c MB RAM\n", Debugger::timer(), round(memory_get_peak_usage(true) / 1024 / 1024)));

} catch (\Exception $e) {
	$invalidConfig = $e instanceof Exception && Exception::INVALID_CONFIG === $e->getCode();
	if ($invalidConfig) {
		echo $generator->colorize($generator->getHeader());
	}

	if ($config->debug) {
		do {
			echo $generator->colorize(sprintf("\n@error@%s@c", $e->getMessage()));
			$trace = $e->getTraceAsString();
		} while ($e = $e->getPrevious());

		printf("\n\n%s\n\n", $trace);
	} else {
		echo $generator->colorize(sprintf("\n@error@%s@c\n\n", $e->getMessage()));
	}

	// Help only for invalid configuration
	if ($invalidConfig) {
		echo $generator->colorize($config->getHelp());
	}

	die(1);
}