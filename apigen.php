<?php

/**
 * API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Ondřej Nešpor (http://andrewsville.cz)
 * Copyright (c) 2011 Jaroslav Hanslík (http://kukulich.cz)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

use NetteX\Diagnostics\Debugger;


require __DIR__ . '/libs/NetteX/nette.min.php';
require __DIR__ . '/libs/fshl/fshl.php';
require __DIR__ . '/libs/Console/ProgressBar.php';
require __DIR__ . '/libs/texy/texy.min.php';
require __DIR__ . '/libs/TokenReflection/compressed.php';
require __DIR__ . '/libs/Apigen/Reflection.php';
require __DIR__ . '/libs/Apigen/Backend.php';
require __DIR__ . '/libs/Apigen/Generator.php';

echo '
Apigen ' . Apigen\Generator::VERSION . '
------------------
';

$options = getopt('', array(
	'source::',
	'destination::',
	'config::',
	'title::',
	'base-uri::',
	'template::',
	'template-dir::',
	'progressbar::',
	'wipeout::'
));

if (isset($options['source'], $options['destination'])) {
	$config = array();
	foreach ($options as $key => $value) {
		$key = preg_replace_callback('#-([a-z])#', function($matches) {
			return ucfirst($matches[1]);
		}, $key);

		if ('off' === strtolower($value)) {
			$value = false;
		} elseif ('on' === strtolower($value)) {
			$value = true;
		}

		$config[$key] = $value;
	}
} elseif (isset($options['config'])) {
	$config = NetteX\Utils\Neon::decode(file_get_contents($options['config']));
} else { ?>
Usage:
	php apigen.php --source=<path> --destination=<path> [options]
	php apigen.php --config=<path>

Options:
	--source        <path>  Name of a source directory to parse
	--destination   <path>  Folder where to save the generated documentation
	--config        <path>  Config file
	--title         <value> Title of generated documentation
	--base-uri      <value> Documentation base URI
	--template      <value> Documentation template name
	--template-dir  <path>  Folder with templates
	--wipeout       On|Off  Wipe out the destination directory first, default On
	--progressbar   On|Off  Display progressbar, default On

<?php
	die();
}

// Default configuration
if (!isset($config['title'])) {
	$config['title'] = '';
}
if (!isset($config['baseUri'])) {
	$config['baseUri'] = '';
}
if (empty($config['template'])) {
	$config['template'] = 'default';
}
if (empty($config['templateDir'])) {
	$config['templateDir'] = __DIR__ . '/templates';
}
if (!isset($config['wipeout'])) {
	$config['wipeout'] = true;
}
if (!isset($config['progressbar'])) {
	$config['progressbar'] = true;
}

Debugger::enable();
Debugger::timer();

$generator = new Apigen\Generator($config);

// Scaning
if (empty($config['source'])) {
	echo "Source directory is not set.\n";
	die();
} elseif (!is_dir($config['source'])) {
	echo "Source directory $config[source] doesn't exist.\n";
	die();
}
echo "Scanning folder $config[source]\n";
list($count, $countInternal) = $generator->parse();
echo "Found $count classes and $countInternal internal classes\n";


// Generating
if (empty($config['destination'])) {
	echo "Destination directory is not set.\n";
	die();
}
echo "Generating documentation to folder $config[destination]\n";
if (is_dir($config['destination']) && $config['wipeout']) {
	echo 'Wiping out destination directory first';
	if ($generator->wipeOutDestination()) {
		echo ", ok\n";
	} else {
		echo ", error\n";
		die();
	}
}

echo "Searching template in $config[templateDir]\n";
echo "Using template $config[template]\n";

$generator->generate();


echo "\nDone. Total time: " . (int) Debugger::timer() . " seconds\n";
