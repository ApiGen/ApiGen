<?php

/**
 * API Generator.
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license", and/or
 * GPL license. For more information please see http://nette.org
 */

require __DIR__ . '/libs/NetteX/loader.php';
require __DIR__ . '/libs/fshl/fshl.php';
require __DIR__ . '/libs/Apigen/CustomClassReflection.php';
require __DIR__ . '/libs/Apigen/Model.php';
require __DIR__ . '/libs/Apigen/Generator.php';

echo '
APIGen version 0.1
------------------
';

if (!isset($_SERVER['argv'][1], $_SERVER['argv'][2])) { ?>
Usage:
	php runner.php <input directory> <output directory>

<?php
	die();
}



date_default_timezone_set('Europe/Prague');
NetteX\Debug::enable();
NetteX\Debug::timer();


$input = $_SERVER['argv'][1];
echo "Scanning folder $input\n";

$model = new Apigen\Model;
$model->parse($input);
$count = count($model->getClasses());

$model->expand();
$countD = count($model->getClasses()) - $count;

echo "Found $count classes and $countD system classes\n";



$output = $_SERVER['argv'][2];
@mkdir($output);

$neon = new NetteX\NeonParser;
$config = $neon->parse(str_replace('%dir%', __DIR__, file_get_contents(__DIR__ . '/config.neon')));

echo "Generating documentation to folder $output\n";
$generator = new Apigen\Generator($model);
$generator->generate($output, $config);

echo 'Done. Total time: ' . (int) NetteX\Debug::timer() . " seconds\n";
