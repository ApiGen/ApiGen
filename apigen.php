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

require __DIR__ . '/libs/Nette/nette.min.php';
require __DIR__ . '/libs/fshl/fshl.php';
require __DIR__ . '/libs/Console/ProgressBar.php';
require __DIR__ . '/libs/texy/texy.min.php';
require __DIR__ . '/libs/TokenReflection/tokenreflection.min.php';
require __DIR__ . '/libs/Apigen/Exception.php';
require __DIR__ . '/libs/Apigen/Config.php';
require __DIR__ . '/libs/Apigen/Template.php';
require __DIR__ . '/libs/Apigen/Reflection.php';
require __DIR__ . '/libs/Apigen/Backend.php';
require __DIR__ . '/libs/Apigen/Generator.php';
require __DIR__ . '/libs/Apigen/Tree.php';

try {

	Nette\Diagnostics\Debugger::enable();
	Nette\Diagnostics\Debugger::timer();

	$options = getopt('c:s:d:h', array(
		'config:',
		'source:',
		'destination:',
		'skip-doc-path:',
		'skip-doc-prefix:',
		'exclude:',
		'title:',
		'base-url:',
		'google-cse:',
		'google-analytics:',
		'template:',
		'template-dir:',
		'allowed-html:',
		'access-levels:',
		'php:',
		'tree:',
		'deprecated:',
		'todo:',
		'source-code:',
		'undocumented:',
		'wipeout:',
		'quiet:',
		'progressbar:',
		'debug:',
		'help'
	));

	// Help
	if (empty($options) || isset($options['h']) || isset($options['help'])) {
		echo Apigen\Generator::getHeader();
		echo Apigen\Config::getHelp();
		die();
	}

	// Start
	$config = new Apigen\Config($options);
	$config->parse();

	$generator = new Apigen\Generator($config);
	$generator->output(Apigen\Generator::getHeader());

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
	list($countClasses, $countConstants, $countFunctions, $countClassesInternal) = $generator->parse();
	$generator->output(sprintf("Found %d classes, %d constants, %d functions and other %d used PHP internal classes\n", $countClasses, $countConstants, $countFunctions, $countClassesInternal));

	// Generating
	$generator->output(sprintf("Searching template in %s\n", $config->templateDir));
	$generator->output(sprintf("Using template %s\n", $config->template));
	if ($config->wipeout && is_dir($config->destination)) {
		$generator->output("Wiping out destination directory\n");
		if (!$generator->wipeOutDestination()) {
			throw new Exception('Cannot wipe out destination directory');
		}
	}

	$generator->output(sprintf("Generating documentation to directory %s\n", $config->destination));
	$skipping = array_merge($config->skipDocPath, $config->skipDocPrefix);
	if (count($skipping) > 1) {
		$generator->output(sprintf("Will not generate documentation for\n %s\n", implode("\n ", $skipping)));
	} elseif (!empty($skipping)) {
		$generator->output(sprintf("Will not generate documentation for %s\n", $skipping[0]));
	}
	$generator->generate();

	// End
	$generator->output(sprintf("Done. Total time: %d seconds, used: %d MB RAM\n", Nette\Diagnostics\Debugger::timer(), round(memory_get_peak_usage(true) / 1024 / 1024)));

} catch (Exception $e) {
	if ($e instanceof Apigen\Exception && Apigen\Exception::INVALID_CONFIG === $e->getCode()) {
		echo Apigen\Generator::getHeader();
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
	if ($e instanceof Apigen\Exception && Apigen\Exception::INVALID_CONFIG === $e->getCode()) {
		echo Apigen\Config::getHelp();
	}

	die(1);
}