<?php

/**
 * ApiGen - API Generator.
 * http://apigen.org
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

use Nette\Diagnostics\Debugger;


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

try {

	$name = sprintf('ApiGen %s', Apigen\Generator::VERSION);
	echo $name . "\n";
	echo str_repeat('-', strlen($name)) . "\n";

	Debugger::enable();
	Debugger::timer();

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
		'template:',
		'template-dir:',
		'allowed-html:',
		'access-levels:',
		'deprecated:',
		'code:',
		'wipeout:',
		'progressbar:',
		'debug:',
		'help'
	));

	// Help
	if (isset($options['h']) || isset($options['help'])) {
		echo Apigen\Config::getHelp();
		die();
	}

	// Start
	$config = new Apigen\Config($options);
	$config->parse();
	$generator = new Apigen\Generator($config);

	// Scan
	if (count($config->source) > 1) {
		printf("Scanning\n %s\n", implode("\n ", $config->source));
	} else {
		printf("Scanning %s\n", $config->source[0]);
	}
	if (count($config->exclude) > 1) {
		printf("Excluding\n %s\n", implode("\n ", $config->exclude));
	} elseif (!empty($config->exclude)) {
		printf("Excluding %s\n", $config->exclude[0]);
	}
	list($count, $countInternal) = $generator->parse();
	printf("Found %d classes and other %d used internal classes\n", $count, $countInternal);

	// Generating
	printf("Searching template in %s\n", $config->templateDir);
	printf("Using template %s\n", $config->template);
	if ($config->wipeout && is_dir($config->destination)) {
		echo "Wiping out destination directory\n";
		if (!$generator->wipeOutDestination()) {
			throw new Exception('Cannot wipe out destination directory');
		}
	}

	printf("Generating documentation to directory %s\n", $config->destination);
	$skipping = array_merge($config->skipDocPath, $config->skipDocPrefix);
	if (count($skipping) > 1) {
		printf("Will not generate documentation for\n %s\n", implode("\n ", $skipping));
	} elseif (!empty($skipping)) {
		printf("Will not generate documentation for %s\n", $skipping[0]);
	}
	$generator->generate();

	// End
	printf("Done. Total time: %d seconds, used: %d MB RAM\n", Debugger::timer(), round(memory_get_peak_usage(true) / 1024 / 1024));

} catch (Exception $e) {
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