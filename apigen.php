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


Debugger::enable();
Debugger::timer();

$name = sprintf('Apigen %s', Apigen\Generator::VERSION);
echo $name . "\n";
echo str_repeat('-', strlen($name)) . "\n";

$options = getopt('', array(
	'config:',
	'source:',
	'destination:',
	'title:',
	'base-uri:',
	'template:',
	'template-dir:',
	'access-levels:',
	'wipeout:',
	'progressbar:'
));

if (isset($options['config'])) {
	$config = NetteX\Utils\Neon::decode(file_get_contents($options['config']));
} elseif (isset($options['source'], $options['destination'])) {
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
		if ('accessLevels' === $key) {
			$value = array_fill_keys(explode(',', $value), true);
		}

		$config[$key] = $value;
	}
} else { ?>
Usage:
	php apigen.php --config=<path>
	php apigen.php --source=<path> --destination=<path> [options]

Options:
	--config        <path>  Config file
	--source        <path>  Name of a source directory to parse
	--destination   <path>  Folder where to save the generated documentation
	--title         <value> Title of generated documentation
	--base-uri      <value> Documentation base URI
	--template      <value> Documentation template name
	--template-dir  <path>  Folder with templates
	--access-levels <list>  Generate documetation for methods and properties with given access level, default public,protected
	--wipeout       On|Off  Wipe out the destination directory first, default On
	--progressbar   On|Off  Display progressbar, default On

<?php
	die();
}


// Default configuration
$defaultConfig = array(
	'title' => '',
	'baseUri' => '',
	'template' => '',
	'templateDir' => '',
	'accessLevels' => array(
		'public' => true,
		'protected' => true,
		'private' => false
	),
	'wipeout' => true,
	'progressbar' => true
);

$config = array_merge($defaultConfig, $config);

// Fix configuration
if (empty($config['template'])) {
	$config['template'] = 'default';
}
if (empty($config['templateDir'])) {
	$config['templateDir'] = __DIR__ . DIRECTORY_SEPARATOR  . 'templates';
}
foreach (array('source', 'destination', 'templateDir') as $key) {
	if (is_dir($config[$key])) {
		$config[$key] = realpath($config[$key]);
	}
}
foreach (array('public', 'protected', 'private') as $level) {
	if (!isset($config['accessLevels'][$level])) {
		$config['accessLevels'][$level] = false;
	}
}

// Searching template
if (!is_dir($config['templateDir'])) {
	echo "Template directory doesn't exist.\n";
	die();
}
echo "Searching template in $config[templateDir]\n";

$templatePath = $config['templateDir'] . DIRECTORY_SEPARATOR . $config['template'];
if (!is_dir($templatePath)) {
	echo "Template doesn't exist.\n";
	die();
}
echo "Using template $config[template]\n";

$templateConfigPath = $templatePath . DIRECTORY_SEPARATOR . 'config.neon';
if (!is_file($templateConfigPath)) {
	echo "Template config doesn't exist.\n";
	die();
}

$config = array_merge($config, NetteX\Utils\Neon::decode(file_get_contents($templateConfigPath)));


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

if ($config['wipeout'] && is_dir($config['destination'])) {
	echo 'Wiping out destination directory first';
	if ($generator->wipeOutDestination()) {
		echo ", ok\n";
	} else {
		echo ", error\n";
		die();
	}
}

$generator->generate();


echo "\nDone. Total time: " . (int) Debugger::timer() . " seconds\n";
