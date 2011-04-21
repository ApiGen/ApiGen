<?php

/**
 * API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

use NetteX\Diagnostics\Debugger;


require __DIR__ . '/libs/NetteX/nette.min.php';
require __DIR__ . '/libs/fshl/fshl.php';
require __DIR__ . '/libs/Console/ProgressBar.php';
require __DIR__ . '/libs/texy/texy.min.php';
require __DIR__ . '/libs/Apigen/CustomClassReflection.php';
require __DIR__ . '/libs/Apigen/Model.php';
require __DIR__ . '/libs/Apigen/Generator.php';

echo '
APIGen version 0.1
------------------
';

$options = getopt('s:d:c:t:l:wpb:');

if (!isset($options['s'], $options['d'])) { ?>
Usage:
	php apigen.php [options]

Options:
	-s <path>  Name of a source directory to parse. Required.
	-d <path>  Folder where to save the generated documentation. Required.
	-c <path>  Output config file.
	-t ...     Title of generated documentation.
	-l ...     Documentation template name
	-w         Wipe out the target directory first
	-p         Display progressbar
	-b <value> Documentation base URI

<?php
	die();
}



date_default_timezone_set('Europe/Prague');
Debugger::enable();
Debugger::timer();



echo "Scanning folder $options[s]\n";
$model = new Apigen\Model;
$model->parse($options['s']);
$count = count($model->getClasses());

$model->expand();
$countD = count($model->getClasses()) - $count;

echo "Found $count classes and $countD system classes\n";



$template = isset($options['l']) ? $options['l'] : 'default';
echo "Using template $template\n";



$configPath = isset($options['c']) ? $options['c'] : __DIR__ . '/config.neon';
$config = file_get_contents($configPath);
$config = strtr($config, array('%template%' => $template, '%dir%' => dirname($configPath)));
$config = NetteX\Utils\Neon::decode($config);
if (isset($options['t'])) {
	$config['variables']['title'] = $options['t'];
}
if (isset($options['b'])) {
	$config['variables']['baseUri'] = $options['b'];
}
$config['settings']['progressbar'] = isset($options['p']);



echo "Generating documentation to folder $options[d]\n";
$generator = new Apigen\Generator($model);
if (is_dir($options['d']) && isset($options['w'])) {
	echo 'Wiping out target directory first';
	if ($generator->wipeOutTarget($options['d'], $config)) {
		echo ", ok\n";
	} else {
		echo ", error\n";
		die();
	}
}
@mkdir($options['d']);
$generator->generate($options['d'], $config);



echo "\nDone. Total time: " . (int) Debugger::timer() . " seconds\n";
